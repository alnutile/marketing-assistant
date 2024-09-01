<script setup>
import {Head, Link} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";

const props = defineProps({
    campaigns: Object,
    copy: String
})


</script>

<template>
    <AppLayout title="Campaigns">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Campaigns
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="flex justify-between items-start">
                        <div class="prose p-4" v-html="copy">

                        </div>
                        <div class="flex justify-end gap-4 p-4">
                            <Link :href="route('campaigns.create')" class="btn btn-primary rounded-none">Create</Link>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <div v-if="campaigns.data.length == 0">
                                <div class="text-center">
                                    <div class="mb-4">
                                        No Campaigns Yet!
                                    </div>
                                    <Link
                                        :href="route('campaigns.create')"
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
                                    <th>View</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- row 1 -->
                                <tr class="bg-base-200" v-for="campaign in campaigns.data" :key="campaign.id">
                                    <th>{{ campaign.id }}</th>
                                    <th>
                                        <div class="avatar">
                                            <div class="w-8 rounded-full">
                                                <img :src="campaign.user?.profile_photo_url" />
                                            </div>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="flex items-center gap-2">

                                            <div>{{ campaign.name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <Link
                                            class="link"
                                            :href="route('campaigns.show', campaign.id)">view</Link>
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
