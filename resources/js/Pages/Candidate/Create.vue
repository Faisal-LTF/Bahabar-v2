<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";

import { useToast } from 'primevue/usetoast';

const toast = useToast();


const props = defineProps({
    show: Boolean,
    title: String,
    events: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["close"]);
const events = ref(props.events);
const form = useForm({
    name: "",
    description: "",
    event_id: null,
    regional_id: null,
    regency_id: null,
    photo: null,
});

onMounted(async () => {
    if (!events.value.length) {
        try {
            const response = await axios.get('/candidates/create');
            events.value = response.data;
        } catch (error) {
            console.error('Error fetching events:', error);
        }
    }
});

const onUpload = (event) => {
    const file = event.files[0];
    form.photo = file;
    toast.add({ severity: 'info', summary: 'Success', detail: 'File Uploaded', life: 3000 });

};

const store = () => {
    form.post(route("candidates.store"), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};

watch(() => form.event_id, (newEventId) => {
    if (newEventId) {
        const selectedEvent = events.value.find(event => event.id === newEventId);
        if (selectedEvent) {
            form.regional_id = selectedEvent.regional_id;
            form.regency_id = selectedEvent.regency_id;
        } else {
            form.regional_id = null;
            form.regency_id = null;
        }
    } else {
        form.regional_id = null;
        form.regency_id = null;
    }
});

watch(() => props.show, (newVal) => {
    if (newVal) {
        form.errors = {};
        form.name = "";
        form.description = "";
        form.event_id = null;
        form.regional_id = null;
        form.regency_id = null;
        form.photo = null;
    }
});
</script>



<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Add ' + props.title" :style="{ width: '30rem' }"
        :closable="false">
        <form @submit.prevent="store" enctype="multipart/form-data">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="name">Name</label>
                    <InputText id="name" v-model="form.name" class="flex-auto" autocomplete="off" placeholder="Name" />
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="event_id">Event</label>
                    <Select v-model="form.event_id" :options="events" optionValue="id" optionLabel="name"
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
                    <div class="font-semibold text-xl mb-4">Upload Photo</div>
                    <FileUpload name="photo" @uploader="onUpload" accept="image/*" :maxFileSize="1000000"
                        customUpload />
                    <small v-if="form.errors.photo" class="text-red-500">{{ form.errors.photo }}</small>
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Save"></Button>
                </div>
            </div>
        </form>
    </Dialog>


</template>


<style scoped lang="scss">
/* Add your styles here */
</style>
