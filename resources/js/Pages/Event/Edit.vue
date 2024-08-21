<script setup>
import { useForm } from "@inertiajs/vue3";
import { watchEffect } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
    event: Object,  // Menggunakan objek event
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    start_date: "",
    end_date: "",
    voting_type: "",
    description: "",
});

const update = () => {
    form.patch(route("event.update", props.event?.id), {  // Mengganti PUT dengan PATCH
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
        form.name = props.event?.name;
        form.start_date = props.event?.start_date;
        form.end_date = props.event?.end_date;
        form.voting_type = props.event?.voting_type;
        form.description = props.event?.description;
    }
});

</script>
<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Update ' + props.title" :style="{ width: '30rem' }" :closable="false">
        <form @submit.prevent="update">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="name">Event Name</label>
                    <InputText id="name" v-model="form.name" class="flex-auto" autocomplete="off" placeholder="Event Name" />
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="start_date">Start Date</label>
                    <InputText id="start_date" v-model="form.start_date" type="date" placeholder="Start Date" />
                    <small v-if="form.errors.start_date" class="text-red-500">{{ form.errors.start_date }}</small>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="end_date">End Date</label>
                    <InputText id="end_date" v-model="form.end_date" type="date" placeholder="End Date" />
                    <small v-if="form.errors.end_date" class="text-red-500">{{ form.errors.end_date }}</small>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="voting_type">Voting Type</label>
                    <Select id="voting_type" v-model="form.voting_type" :options="[
                        { label: 'Gratis', value: 'gratis' },
                        { label: 'Berbayar', value: 'berbayar' }
                    ]" optionLabel="label" optionValue="value" placeholder="Select Voting Type" />
                    <small v-if="form.errors.voting_type" class="text-red-500">{{ form.errors.voting_type }}</small>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="description">Description</label>
                    <Textarea id="description" v-model="form.description" placeholder="Description" />
                    <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
                </div>

                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
