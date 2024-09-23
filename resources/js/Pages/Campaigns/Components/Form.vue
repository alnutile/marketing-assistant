<script setup>
import { onMounted, ref } from 'vue';
import {useForm} from "@inertiajs/vue3";
import SectionBorder from "@/Components/SectionBorder.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";

defineEmits(['update:modelValue']);


defineProps({
    modelValue: String,
});


</script>

<template>
    <div class="col-span-12 sm:col-span-6">
        <InputLabel for="name" value="Name" />
        <TextInput
            id="name"
            v-model="modelValue.name"
            type="text"
            class="mt-1 block w-full"
            required
            autocomplete="name"
        />
        <InputError :message="modelValue.errors.name" class="mt-2" />
    </div>

    <div class="col-span-12 sm:col-span-6">
        <InputLabel for="Content" value="Prompt" />
        <textarea
            v-model="modelValue.content"
            class="w-full border-gray-300 textarea textarea-bordered" id="content" rows="20">

                </textarea>
        <InputError :message="modelValue.errors.content" class="mt-2" />
        <div class="border shadow-sm  p-4 border-gray-200 mt-2">
            This is the prompt used to kick off the project. It will be used to create the first message.
        </div>
        <SectionBorder />
    </div>

    <div class="col-span-12 sm:col-span-6">
        <InputLabel for="Content" value="System Prompt" />
        <textarea
            v-model="modelValue.system_prompt"
            class="w-full border-gray-300 textarea textarea-bordered" id="content" rows="20">

                </textarea>
        <InputError :message="modelValue.errors.system_prompt" class="mt-2" />
        <div class="border shadow-sm  p-4 border-gray-200 mt-2">
            This will help guide the ai overall with all interaction. For example
            "You are a helpful assistant" or "You are an assistant to contractors using this tool to help organize their contracting business."
        </div>
        <SectionBorder />
    </div>

    <div class="col-span-12 sm:col-span-6">
        <InputLabel for="Content" value="Scheduler Prompt" />
        <textarea
            v-model="modelValue.scheduler_prompt"
            class="w-full border-gray-300 textarea textarea-bordered" id="content" rows="20">

                </textarea>
        <InputError :message="modelValue.errors.scheduler_prompt" class="mt-2" />
        <div class="border shadow-sm  p-4 border-gray-200 mt-2">
            The system will check for this prompt every 30 minutes.
            So you can use it to create new tasks or post to social media or run your automations.
        </div>
        <SectionBorder />
    </div>

    <div class="col-span-12 sm:col-span-6">
        <InputLabel for="target_audience" value="Target Audience" />
        <textarea
            v-model="modelValue.target_audience"
            class="w-full border-gray-300 textarea textarea-bordered"
            id="target_audience" rows="5">

                </textarea>
        <InputError :message="modelValue.errors.target_audience" class="mt-2" />
    </div>

    <div class="col-span-6 sm:col-span-3">
        <InputLabel for="start_date" value="Start Date" />
        <TextInput
            id="start_date"
            v-model="modelValue.start_date"
            type="date"
            class="mt-1 block w-full"
            required
        />
        <InputError :message="modelValue.errors.email" class="mt-2" />
    </div>

    <div class="col-span-6 sm:col-span-3">
        <InputLabel for="end_date" value="End Date" />
        <TextInput
            id="end_date"
            v-model="modelValue.end_date"
            type="date"
            class="mt-1 block w-full"
            required
        />
        <InputError :message="modelValue.errors.email" class="mt-2" />
    </div>

    <div class="col-span-6 sm:col-span-3">
        <InputLabel for="status" value="Status" />
        <select
            v-model="modelValue.status"
            class="select select-bordered w-full max-w-xs">
            <template v-for="(status, value) in statuses">
                <option :value="status.id">
                    {{ status.name }}
                </option>
            </template>
        </select>
        <InputError :message="modelValue.errors.status" class="mt-2" />
    </div>

    <div class="col-span-6 sm:col-span-3">
        <InputLabel for="status" value="Product of Service" />
        <select
            v-model="modelValue.product_or_service"
            class="select select-bordered w-full max-w-xs">
            <template v-for="service in productServices" :key="service.id">
                <option :value="service.id">
                    {{ service.name }}
                </option>
            </template>
        </select>
        <InputError :message="modelValue.errors.product_or_service" class="mt-2" />
    </div>

    <div class="col-span-6 sm:col-span-4">
        <InputLabel for="budget" value="Budget" />
        <label class="input input-bordered flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            <input
                v-model="modelValue.budget"
                type="text" class="grow border-none" placeholder="2000" />
        </label>
        <InputError :message="modelValue.errors.budget" class="mt-2" />
    </div>
</template>

<style scoped>

</style>
