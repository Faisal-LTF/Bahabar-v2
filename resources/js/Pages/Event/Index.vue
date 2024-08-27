<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import Create from "@/Pages/Event/Create.vue";
import Edit from "@/Pages/Event/Edit.vue";
import { usePage, useForm } from '@inertiajs/vue3';
import { onMounted, reactive, ref, watch, computed } from "vue";
import pkg from "lodash";
import { router } from "@inertiajs/vue3";
const { _, debounce, pickBy } = pkg;
import { loadToast } from '@/composables/loadToast';

const props = defineProps({
    title: String,
    filters: Object,
    events: Object,
    perPage: Number,
});

loadToast();

const deleteDialog = ref(false);
const form = useForm({});

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        createOpen: false,
        editOpen: false,
    },
    event: null,
});

const deleteData = () => {
    deleteDialog.value = false;

    form.delete(route("event.destroy", data.event?.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
}

const onPageChange = (event) => {
    router.get(route('event.index'), { page: event.page + 1 }, { preserveState: true });
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        router.get(route("event.index"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        });
    }, 150)
);

// Fungsi untuk memformat tanggal
const formatDate = (date) => {
    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(date).toLocaleDateString('en-GB', options).replace(",", " ");
};

</script>

<template>
    <app-layout>
        <div class="card">
            <Create :show="data.createOpen" @close="data.createOpen = false" :title="props.title" />
            <Edit :show="data.editOpen" @close="data.editOpen = false" :event="data.event" :title="props.title" />

            <Button v-show="can(['create event'])" label="Create" @click="data.createOpen = true" icon="pi pi-plus" />

            <DataTable lazy :value="events.data" paginator :rows="events.per_page" :totalRecords="events.total"
                :first="(events.current_page - 1) * events.per_page" @page="onPageChange" tableStyle="min-width: 50rem">
                <template #header>
                    <div class="flex justify-end">
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="data.params.search" placeholder="Keyword Search" />
                        </IconField>
                    </div>
                </template>
                <template #empty> No data found. </template>
                <template #loading> Loading data. Please wait. </template>

                <Column header="No">
                    <template #body="slotProps">
                        {{ slotProps.index + 1 }}
                    </template>
                </Column>
                <Column field="name" header="Name"></Column>

                <!-- Tampilkan nama provinsi -->
                <Column field="regional_name" header="Provinsi"></Column>

                <!-- Tampilkan nama kabupaten/kota -->
                <Column field="regency_name" header="Kab/Kota"></Column>

                <Column field="start_date" header="Start Date"></Column>
                <Column field="end_date" header="End Date"></Column>
                <Column field="voting_type" header="Voting Type"></Column>
                <Column field="description" header="Description"></Column>
                <!-- <Column field="created_at" header="Created">
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.created_at) }}
                    </template>
                </Column>
                <Column field="updated_at" header="Updated">
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.updated_at) }}
                    </template>
                </Column> -->
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button v-show="can(['update event'])" icon="pi pi-pencil" outlined rounded class="mr-2" @click="
                            (data.editOpen = true),
                            (data.event = slotProps.data)" />
                        <Button v-show="can(['delete event'])" icon="pi pi-trash" outlined rounded severity="danger"
                            @click="deleteDialog = true; data.event = slotProps.data" />
                    </template>
                </Column>
            </DataTable>

            <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                <div class="flex items-center gap-4">
                    <i class="pi pi-exclamation-triangle !text-3xl" />
                    <span v-if="data.event">Are you sure you want to delete <b>{{ data.event.name }}</b>?</span>
                </div>
                <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" />
                    <Button label="Yes" icon="pi pi-check" @click="deleteData" />
                </template>
            </Dialog>
        </div>
    </app-layout>
</template>

<style scoped lang="scss"></style>
