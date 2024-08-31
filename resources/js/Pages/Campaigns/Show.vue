<script setup>
import {Link, useForm} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import {ref} from "vue";
import Kickoff from "@/Pages/Campaigns/Components/Kickoff.vue";
import InputError from "@/Components/InputError.vue";
import Clipboard from "@/Components/Clipboard.vue";
import Index from "@/Pages/Tasks/Index.vue";

const props = defineProps({
    campaign: Object,
    messages: Object
})

const form = useForm({
    input: ''
})

const chat = () => {
    form.post(route('chat.chat', {
        campaign: props.campaign.data.id
    }), {
        errorBag: 'chat',
        preserveScroll: true,
    });
}

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
                    <div class="flex justify-between p-4">
                        <div>
                            <div class="flex justify-between gap-2">
                                <span class="badge badge-accent">
                                    status: {{ campaign.data.status_formatted }}
                                </span>

                                <span class="badge badge-neutral">
                                    ai: {{ campaign.data.chat_status_formatted }}
                                 </span>

                                <span class="badge badge-secondary">
                                    start: {{ campaign.data.start_date }}
                                 </span>

                                <span class="badge badge-ghost">
                                    end: {{ campaign.data.end_date }}
                                 </span>

                            </div>
                        </div>

                        <div class="flex justify-end gap-2 items-center">
                            <Kickoff :campaign="campaign.data"/>
                            <Link
                                :href="route('campaigns.edit', campaign.data.id)"
                                class="btn btn-primary rounded-none">Edit</Link>
                        </div>
                    </div>


                    <div class="grid grid-cols-1 sm:grid-cols-12 p-4">
                        <div class="col-span-8">
                            <div v-auto-animate>
                                <template v-for="message in messages.data">

                                    <div class="border border-gray-300 rounded-md p-4 mb-4 overflow-scroll ">
                                        <div class="flex justify-end gap-2 items-center">
                                            <Clipboard :content="message.content_raw"/>
                                        </div>
                                        <div  class="font-bold" v-if="message.role != 'user'">
                                            Assistant <span class="badge badge-ghost text-xs">{{ message.updated_at }}</span>
                                        </div>
                                        <div v-else class="font-bold">
                                            User: {{ message.user?.name }}  <span class="badge badge-ghost text-xs">{{ message.updated_at }}</span>
                                        </div>
                                        <div class="prose" v-html="message.content"></div>

                                        <div class="flex justify-end gap-2 items-center">
                                            <Clipboard :content="message.content_raw"/>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="col-span-4 ml-2">

                            <form @submit.prevent="chat">
                                <InputError :message="form.errors.input" class="mt-2" />

                                <textarea
                                    v-model="form.input"
                                    placeholder="Ask a question..."
                                    required
                                    class="textarea textarea-bordered textarea-lg w-full"></textarea>
                                <div class="flex justify-end mt-2">
                                    <button
                                        :disabled="form.processing"
                                        class="btn btn-sm btn-secondary rounded-none" @click="chat">
                                        <span v-if="!form.processing">Chat</span>
                                        <span v-else class="loading loading-dots loading-lg"></span>
                                    </button>
                                </div>
                            </form>

                            <div>
                                <h2 class="font-bold flex justify-start gap-2 items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>

                                    Tasks</h2>

                                <Index :campaign="campaign.data"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>

</style>
