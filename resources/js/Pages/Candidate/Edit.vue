<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch, computed } from "vue";


const props = defineProps({
    show: Boolean,
    title: String,
    candidate: Object, // Mengambil data candidate yang akan diedit
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

// Fetch events data if not already provided
onMounted(async () => {
    if (!events.value.length) {
        try {
            const response = await axios.get('/candidates/create');
            events.value = response.data;
            console.log("Fetched events:", events.value);  // Debugging: Check fetched events
        } catch (error) {
            console.error('Error fetching events:', error);
        }
    }

    // Initialize form with candidate data if available
    if (props.candidate) {
        form.name = props.candidate.name || "";
        form.description = props.candidate.description || "";
        form.event_id = props.candidate.event_id || null;
        form.regional_id = props.candidate.regional_id || null;
        form.regency_id = props.candidate.regency_id || null;
    }
});

// Watch for changes in candidate data and update form accordingly
watch(() => props.candidate, (newCandidate) => {
    if (newCandidate) {
        form.name = newCandidate.name || "";
        form.description = newCandidate.description || "";
        form.event_id = newCandidate.event_id || null;
        form.regional_id = newCandidate.regional_id || null;
        form.regency_id = newCandidate.regency_id || null;
    }
}, { immediate: true });

// Computed property to get selected event name
const selectedEventName = computed(() => {
    const selectedEvent = events.value.find(event => event.id === form.event_id);
    return selectedEvent ? selectedEvent.name : null;
});

// Watch for changes in selected event and update regional and regency IDs
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

const update = () => {
    form.patch(route("candidates.update", props.candidate?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
        },
    });
};
</script>

<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Update ' + props.title"
        :style="{ width: '35rem' }" :closable="false">
        <form @submit.prevent="update">
            <div class="flex flex-col gap-4">
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

                <!-- Buttons -->
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>

        <!-- Debugging: Display form data in template -->
        <pre>{{ events }}</pre>
        <pre>{{ form }}</pre>
    </Dialog>
</template>
