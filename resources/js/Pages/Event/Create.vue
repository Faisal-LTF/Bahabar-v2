<script setup>
import { useForm } from "@inertiajs/vue3";
import { watchEffect } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    start_date: "",
    end_date: "",
    voting_type: "gratis", // Default to 'gratis'
    description: "",
});

const create = () => {
    form.post(route("event.store"), {
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
        <form @submit.prevent="create">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="name">Event Name</label>
                    <InputText id="name" v-model="form.name" class="flex-auto" autocomplete="off"
                        placeholder="Event Name" />
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
                        { label: 'PILIH', value: '' },
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
                    <Button type="submit" label="Save"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
