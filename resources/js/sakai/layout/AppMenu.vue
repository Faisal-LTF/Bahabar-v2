<script setup>
import { ref } from 'vue';
import AppMenuItem from './AppMenuItem.vue';

const model = ref([
    {
        label: 'Home',
        items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/dashboard' }]
    },
    {
        label: 'Setting',
        items: [
            { label: 'User', icon: 'pi pi-fw pi-user', to: '/user', can: 'read user' },
            { label: 'Role', icon: 'pi pi-fw pi-id-card', to: '/role', can: 'read role' },
            { label: 'Permission', icon: 'pi pi-fw pi-mobile', to: '/permission', class: 'rotated-icon', can: 'read permission' }
        ]
    },
    {
        label: 'Event Managamenet',
        items: [
            { label: 'Events', icon: 'pi pi-fw pi-calendar', to: '/events', can: 'read event' },
            { label: 'Candidates', icon: 'pi pi-fw pi-users', to: '/candidates', can: 'read candidate' },
            { label: 'Votes', icon: 'pi pi-fw pi-chart-line', to: '/votes', can: 'read vote' }
        ]
    },
    {
        items: [
            {
                label: 'Template Source',
                icon: 'pi pi-fw pi-github',
                url: 'https://sakai.primevue.org/',
                target: '_blank'
            }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <hr>
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
