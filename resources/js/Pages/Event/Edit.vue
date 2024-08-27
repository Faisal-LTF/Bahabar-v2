<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, watchEffect, onMounted, computed } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
    event: Object,
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    start_date: "",
    end_date: "",
    voting_type: "",
    description: "",
    regional_id: null,
    regency_id: null,
    status: props.event?.status || "ready",
});

const provinces = ref([]);
const regencies = ref([]);

const fetchProvinces = async () => {
    try {
        const response = await axios.get('/api/provinces');
        provinces.value = response.data;
    } catch (error) {
        console.error('Error fetching provinces:', error);
    }
};

const fetchRegencies = async (provinceId) => {
    try {
        const response = await axios.get(`/api/regencies/${provinceId}`);
        regencies.value = response.data;
    } catch (error) {
        console.error('Error fetching regencies:', error);
    }
};

onMounted(async () => {
    await fetchProvinces();

    if (props.event?.regional_id) {
        await fetchRegencies(props.event.regional_id);
    }

    form.regional_id = props.event?.regional_id || null;
    form.regency_id = props.event?.regency_id || null;
});

watchEffect(async () => {
    if (form.regional_id) {
        await fetchRegencies(form.regional_id);
    }
});

const update = () => {
    form.patch(route("event.update", props.event?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
        },
    });
};

const selectedProvinceName = computed(() => {
    const province = provinces.value.find(p => p.id === form.regional_id);
    return province ? province.name : 'Select Province';
});

const selectedRegencyName = computed(() => {
    const regency = regencies.value.find(r => r.id === form.regency_id);
    return regency ? regency.name : 'Select Regency';
});

watchEffect(() => {
    if (props.show) {
        form.errors = {};
        form.name = props.event?.name || "";
        form.start_date = props.event?.start_date || "";
        form.end_date = props.event?.end_date || "";
        form.voting_type = props.event?.voting_type || "";
        form.description = props.event?.description || "";
        form.regional_id = props.event?.regional_id || null;
        form.regency_id = props.event?.regency_id || null;
        form.status = props.event?.status || "ready";
    }
});
</script>

<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Update ' + props.title"
        :style="{ width: '30rem' }" :closable="false">
        <form @submit.prevent="update">
            <div class="flex flex-col gap-4">
                <!-- Event Name -->
                <div class="flex flex-col gap-2">
                    <label for="name">Event Name</label>
                    <InputText id="name" v-model="form.name" class="flex-auto" autocomplete="off"
                        placeholder="Event Name" />
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>

                <!-- Provinsi -->
                <div class="flex flex-col gap-2">
                    <label for="regional_id">Provinsi</label>
                    <Select id="regional_id" v-model="form.regional_id" :options="provinces" optionLabel="name"
                        optionValue="id" :placeholder="props.event.regional_name || 'Select Province'" />
                    <small v-if="form.errors.regional_id" class="text-red-500">{{ form.errors.regional_id }}</small>
                </div>

                <!-- Kabupaten/Kota -->
                <div class="flex flex-col gap-2">
                    <label for="regency_id">Kabupaten/Kota</label>
                    <Select id="regency_id" v-model="form.regency_id" :options="regencies" optionLabel="name"
                        optionValue="id" :placeholder="props.event.regency_name || 'Select Regency'" />
                    <small v-if="form.errors.regency_id" class="text-red-500">{{ form.errors.regency_id }}</small>
                </div>

                <!-- Start Date -->
                <div class="flex flex-col gap-2">
                    <label for="start_date">Start Date</label>
                    <InputText id="start_date" v-model="form.start_date" type="date" placeholder="Start Date" />
                    <small v-if="form.errors.start_date" class="text-red-500">{{ form.errors.start_date }}</small>
                </div>

                <!-- End Date -->
                <div class="flex flex-col gap-2">
                    <label for="end_date">End Date</label>
                    <InputText id="end_date" v-model="form.end_date" type="date" placeholder="End Date" />
                    <small v-if="form.errors.end_date" class="text-red-500">{{ form.errors.end_date }}</small>
                </div>

                <!-- Voting Type -->
                <div class="flex flex-col gap-2">
                    <label for="voting_type">Voting Type</label>
                    <Select id="voting_type" v-model="form.voting_type" :options="[
                        { label: 'Gratis', value: 'gratis' },
                        { label: 'Berbayar', value: 'berbayar' }
                    ]" optionLabel="label" optionValue="value" placeholder="Select Voting Type" />
                    <small v-if="form.errors.voting_type" class="text-red-500">{{ form.errors.voting_type }}</small>
                </div>

                <!-- Description -->
                <div class="flex flex-col gap-2">
                    <label for="description">Description</label>
                    <Textarea id="description" v-model="form.description" placeholder="Description" />
                    <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
                </div>
                <!-- Status -->
                <div class="flex flex-col gap-2">
                    <label for="status">Status</label>
                    <Select id="status" v-model="form.status" :options="[
                        { label: 'Ready', value: 'Ready' },
                        { label: 'In Progress', value: 'Progress' },
                        { label: 'Completed', value: 'Completed' },
                        { label: 'Cancelled', value: 'Cancelled' }
                    ]" optionLabel="label" optionValue="value" placeholder="Select Status" />
                    <small v-if="form.errors.status" class="text-red-500">{{ form.errors.status }}</small>
                </div>
                <!-- Buttons -->
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
