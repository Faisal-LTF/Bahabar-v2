<script setup>
import { useForm } from "@inertiajs/vue3";
import { watchEffect, ref, onMounted } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
    events: {
        type: Array,
        default: () => [], // Default to empty array
    },
});

onMounted(() => {
    console.log('Props:', props); // Memastikan props diterima dengan benar
    console.log('Events:', props.events); // Memastikan events diterima dengan benar
});



const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    region: "",
    description: "",
    event_id: null,  // Associate the candidate with an event
    photo: null,  // Photo will be uploaded
});


const onUpload = (event) => {
    const file = event.files[0];
    form.photo = file;
};

const create = () => {
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

watchEffect(() => {
    if (props.show) {
        form.errors = {};
    }
});

</script>


<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Add ' + props.title" :style="{ width: '30rem' }"
        :closable="false">
        <form @submit.prevent="create" enctype="multipart/form-data">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="name">Name</label>
                    <InputText id="name" v-model="form.name" class="flex-auto" autocomplete="off" placeholder="Name" />
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="region">Region</label>
                    <InputText id="region" v-model="form.region" class="flex-auto" autocomplete="off"
                        placeholder="Region" />
                    <small v-if="form.errors.region" class="text-red-500">{{ form.errors.region }}</small>
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
