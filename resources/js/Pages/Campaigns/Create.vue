<script setup>
import {Link, useForm} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import FormSection from "@/Components/FormSection.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import Form from "@/Pages/Campaigns/Components/Form.vue";

const props = defineProps({
    project: Object,
    productServices: Array,
    statuses: Array,
    content_start: String,
})


const form = useForm({
    name: 'You Project Name',
    start_date: '',
    end_date: '',
    status: 'draft',
    content: props.content_start,
    system_prompt: '',
    scheduler_prompt: '',
    product_or_service: '',
    target_audience: '',
    budget: '',
});

const save = () => {
    form.post(route('projects.store'), {
        errorBag: 'saveCampaign',
        preserveScroll: true,
    });
}


</script>

<template>
<AppLayout title="Projects">
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <FormSection @submitted="save">>
        <template #title>
            Project Create
        </template>

        <template #description>
            Info here about kicking off your project....
        </template>

        <template #form>

            <Form :modelValue="form">

            </Form>

        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>

    </FormSection>
    </div>
</AppLayout>

</template>


<style scoped>

</style>
