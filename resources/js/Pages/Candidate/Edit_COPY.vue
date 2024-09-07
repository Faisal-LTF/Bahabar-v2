<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted, watchEffect, computed } from "vue";

// Mendefinisikan properti yang diterima oleh komponen
const props = defineProps({
    show: Boolean,
    title: String,
    candidate: Object,
    events: {
        type: Array,
        default: () => [],
    },
});

// Emit untuk menutup dialog
const emit = defineEmits(["close"]);

// Membuat referensi untuk daftar events
const events = ref(props.events);

// Inisialisasi form dengan data kandidat yang ada
const form = useForm({
    name: props.candidate?.name || "",
    description: props.candidate?.description || "",
    event_id: props.candidate?.event_id || null,
    photo: null,
    _method: 'PATCH',
});

// Fungsi untuk menangani upload file
const onUpload = (event) => {
    const file = event.files[0];  // Mengambil file dari event.files, bukan event.target.files
    if (file) {
        form.photo = file;  // Menyimpan file ke form.photo
    }
};


// Mengambil data events jika belum ada
onMounted(async () => {
    if (!events.value.length) {
        try {
            const response = await axios.get('/candidates/create');
            events.value = response.data;
        } catch (error) {
            console.error('Error fetching events:', error);
        }
    }
    // Inisialisasi form dengan data kandidat jika tersedia
    if (props.candidate) {
        form.name = props.candidate.name || "";
        form.description = props.candidate.description || "";
        form.event_id = props.candidate.event_id || null;
        form.regional_id = props.candidate.regional_id || null;
        form.regency_id = props.candidate.regency_id || null;
        form.photoPath = props.candidate.photo || "";
    }
});

// Efek untuk memantau perubahan pada props.show
watchEffect(() => {
    if (props.show) {
        form.errors = {};
        form.name = props.candidate?.name || form.name;
        form.description = props.candidate?.description || form.description;
        form.event_id = props.candidate?.event_id || form.event_id;
        form.regional_id = props.candidate?.regional_id || form.regional_id;
        form.regency_id = props.candidate?.regency_id || form.regency_id;
        form.photoPath = props.candidate?.photo || form.photoPath;
        form.photo = null;
    }
});


// Properti terkomputasi untuk mendapatkan nama event yang dipilih
const selectedEventName = computed(() => {
    const selectedEvent = events.value.find(event => event.id === form.event_id);
    return selectedEvent ? selectedEvent.name : 'Select Event';
});

// Fungsi untuk memperbarui informasi kandidat
const update = () => {
    form.post(route('candidates.update', props.candidate.id), {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            emit('close');
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
};

</script>


<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Update ' + props.title"
        :style="{ width: '35rem' }" :closable="false">
        <form @submit.prevent="update">
            <div class="flex flex-col gap-4">
                <!-- Bagian upload dan ganti foto kandidat -->
                <label for="photo">Foto Kandidat :</label>
                <div v-if="props.candidate?.photo" class="photo-container mb-4">
                    <img :src="'/storage/' + props.candidate.photo" alt="Candidate Photo"
                        class="w-50 h-50 object-cover mb-2" />
                </div>

                <!-- Input untuk mengganti foto -->
                <label>Ganti Foto Kandidat :</label>
                <!-- <div>
                    <input type="file" @change="form.photo = $event.target.files[0]">
                    <small v-if="form.errors.photo" class="text-red-500">{{ form.errors.photo }}</small>
                </div> -->

                <div class="card">
                    <div class="font-semibold text-xl mb-4">Ganti Foto Kandidat :</div>
                    <FileUpload name="photo" @uploader="onUpload" accept="image/*" :maxFileSize="1000000"
                        customUpload />
                    <small v-if="form.errors.photo" class="text-red-500">{{ form.errors.photo }}</small>
                </div>


                <!-- Candidate Name -->
                <div class="flex flex-col gap-2">
                    <label for="name">Candidate Name</label>
                    <InputText id="name" v-model="form.name" class="flex-auto" autocomplete="off"
                        placeholder="Candidate Name" />
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>

                <!-- Event Selection -->
                <div class="flex flex-col gap-2">
                    <label for="event_id">Event</label>
                    <Select id="event_id" v-model="form.event_id" :options="events" optionLabel="name" optionValue="id"
                        :placeholder="selectedEventName || 'Select Event'" />
                    <small v-if="form.errors.event_id" class="text-red-500">{{ form.errors.event_id }}</small>
                </div>

                <!-- Description -->
                <div class="flex flex-col gap-2">
                    <label for="description">Description</label>
                    <Textarea id="description" v-model="form.description" placeholder="Description" />
                    <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
                </div>

                <!-- <div class="card">
                    <div class="font-semibold text-xl mb-4">Upload Photo</div>
                    <FileUpload name="photo" @uploader="onUpload" accept="image/*" :maxFileSize="1000000"
                        customUpload />
                    <small v-if="form.errors.photo" class="text-red-500">{{ form.errors.photo }}</small>
                </div> -->

                <!-- Buttons -->
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
