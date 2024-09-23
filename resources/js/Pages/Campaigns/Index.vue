<script setup>
import {Head, Link} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";

const props = defineProps({
    projects: Object,
    copy: String
})


</script>

<template>
    <AppLayout title="Projects">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Projects
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="flex justify-between items-start">
                        <div class="prose p-4" v-html="copy">

                        </div>
                        <div class="flex justify-end gap-4 p-4">
                            <Link :href="route('projects.create')" class="btn btn-primary rounded-none">Create</Link>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <div v-if="projects.data.length == 0">
                                <div class="text-center">
                                    <div class="mb-4">
                                        No Projects Yet!
                                    </div>
                                    <Link
                                        :href="route('projects.create')"
                                        class="btn btn-primary rounded-none">Create</Link>
                                </div>
                            </div>
                            <table class="table" v-else>
                                <!-- head -->
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Who</th>
                                    <th>Name</th>
                                    <th>Team</th>
                                    <th>View</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- row 1 -->
                                <tr class="bg-base-200" v-for="campaign in projects.data" :key="project.id">
                                    <td>{{ project.id }}</td>
                                    <td>
                                        <div class="avatar">
                                            <div class="w-8 rounded-full">
                                                <img :src="project.user?.profile_photo_url" />
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ project.name }}
                                    </td>

                                    <td>
                                        {{ project.team?.name }}
                                    </td>
                                    <td>
                                        <Link
                                            class="link"
                                            :href="route('projects.show', project.id)">view</Link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>

</style>
