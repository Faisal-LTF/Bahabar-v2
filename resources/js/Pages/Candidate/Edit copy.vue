<script setup>
import { useForm } from "@inertiajs/vue3";
import { watchEffect, onMounted } from 'vue';

const props = defineProps({
    show: Boolean,
    title: String,
    candidate: {
        type: Object,
        default: () => ({
            id: null,
            name: '',
            description: '',
            event_id: null,
            regional_id: null,
            regency_id: null,
        }),
    },
    events: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["close"]);


const form = useForm({
    name: "",
    description: "",
    event_id: null,
    regional_id: null,
    regency_id: null,
    photo: null,
    photoPath: "" // Menambahkan properti photoPath untuk menyimpan path foto yang ada

});

onMounted(() => {
    console.log("Candidate Props:", props.candidate); // Log candidate prop di mounted
    // Set form values based on candidate props
    form.name = props.candidate?.name || "";
    form.description = props.candidate?.description || "";
    form.event_id = props.candidate?.event_id || null;
    form.regional_id = props.candidate?.regional_id || null;
    form.regency_id = props.candidate?.regency_id || null;
    form.photoPath = props.candidate.photo || ""; // Pastikan photoPath diisi dengan benar

});
const onUpload = (event) => {
    const file = event.files[0];
    form.photo = file;
};

const update = () => {
    form.put(route("candidates.update", props.candidate?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};

watchEffect(() => {
    if (props.show) {
        console.log("Candidate ID:", props.candidate?.id); // Log ID kandidat di Vue
        form.errors = {};
        form.name = props.candidate?.name || "";
        form.description = props.candidate?.description || "";
        form.event_id = props.candidate?.event_id || null;
        form.regional_id = props.candidate?.regional_id || null;
        form.regency_id = props.candidate?.regency_id || null;
    }
});


</script>

<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Update ' + props.title"
        :style="{ width: '40rem' }" :closable="false">

        <form @submit.prevent="update" enctype="multipart/form-data">
            <div class="flex flex-col gap-4">
                <!-- Display photo if available -->
                <label for="photo">Foto Kandidat :</label>
                <div v-if="form.photoPath" class="photo-container">
                    <img :src="'/storage/' + form.photoPath" alt="Candidate Photo" class="w-50 h-50 object-cover" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="name">Name</label>
                    <InputText id="name" v-model="form.name" class="flex-auto" autocomplete="off" placeholder="Name" />
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="event_id">Event</label>
                    <Select v-model="form.event_id" :options="props.events" optionValue="id" optionLabel="name"
                        placeholder="Select an Event" />
                    <small v-if="form.errors.event_id" class="text-red-500">{{ form.errors.event_id }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="description">Description</label>
                    <Textarea id="description" v-model="form.description" placeholder="Your Message" :autoResize="true"
                        rows="3" cols="30" />
                    <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
                </div>
                <div class="card">
                    <div class="font-semibold text-xl mb-4">Upload New Photo</div>
                    <FileUpload name="photo" @uploader="onUpload" accept="image/*" :maxFileSize="1000000"
                        customUpload />
                    <small v-if="form.errors.photo" class="text-red-500">{{ form.errors.photo }}</small>
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
