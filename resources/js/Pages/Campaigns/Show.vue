<script setup>
import {Link} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    campaign: Object
})


</script>

<template>
    <AppLayout title="Campaign">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🚀 Campaign: {{ campaign.data.name}}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    Campaign info here
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>

</style>
