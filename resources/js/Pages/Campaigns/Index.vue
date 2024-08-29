<script setup>
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    campaigns: Object
})


</script>

<template>

    <div>
        <h2>
            Campaigns
        </h2>
        <div>
            <Link>Create</Link>
        </div>
        <div>
            Nice Table here...
        </div>
    </div>
</template>

<style scoped>

</style>
