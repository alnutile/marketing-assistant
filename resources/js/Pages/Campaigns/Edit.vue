<script setup>
import {Link, useForm} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import FormSection from "@/Components/FormSection.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import Form from "@/Pages/Campaigns/Components/Form.vue";

const props = defineProps({
    project: Object,
    productServices: Array,
    statuses: Array,
    content_start: String,
})


const form = useForm({
    name: props.project.data.name,
    start_date: props.project.data.start_date,
    end_date: props.project.data.end_date,
    status: props.project.data.status,
    system_prompt: props.project.data.system_prompt,
    scheduler_prompt: props.project.data.scheduler_prompt,
    content: props.project.data.content,
    product_or_service: props.project.data.product_or_service,
    target_audience: props.project.data.target_audience,
    budget: props.project.data.budget,
});

const save = () => {
    form.put(route('projects.update', {
        project: props.project.data.id
    }), {
        errorBag: 'saveCampaign',
        preserveScroll: true,
    });
}


</script>

<template>
<AppLayout title="Project">
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <FormSection @submitted="save">>
        <template #title>
            Project Create
        </template>

        <template #description>
            Info here about kicking off your project....
        </template>

        <template #form>
            <Form :modelValue="form"/>

        </template>

        <template #actions>
            <div class="flex justify-end gap-2">

                <ActionMessage :on="form.recentlySuccessful" class="me-3">
                    Updated.
                </ActionMessage>

                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Update
                </PrimaryButton>

                <a
                    :href="route('projects.show', {
                    project: project.data.id
                })"
                    class="btn btn-secondary">
                    View
                </a>
            </div>
        </template>

    </FormSection>
    </div>
</AppLayout>

</template>


<style scoped>

</style>
