<script setup>

import {onMounted, ref} from "vue";
import {useForm} from "@inertiajs/vue3";

const tasks = ref([])

const props = defineProps({
    campaign: Object
})

const getTasks = () => {
    axios.get(route('tasks.index', {
        campaign: props.campaign.id
    })).then(response => {
        tasks.value = response.data.tasks
    })
}

onMounted(() => {
    getTasks()
})

const completeTaskForm = useForm({})

const completeTask = (task) => {
    completeTaskForm.post(route('tasks.complete', {
        task: task.id
    }), {
        preserveScroll: true,
        onFinish: () => {
            getTasks()
        }
    })
}
</script>

<template>

    <div v-auto-animate>
        <template v-for="task in tasks" :key="task.id">
            <div class="p-2 border border-gray-300 rounded-md my-2">
                <div class="text-gray-600 text-md flex justify-between items-center">
                    {{ task.name }} <span class="badge badge-ghost text-xs">{{ task.id }} </span>
                </div>


                <div class="whitespace-pre-wrap text-gray-600 text-sm prose py-2 px-1">
                    {{ task.details }}
                </div>

                <div class="flex justify-start gap-2 items-center">
                    <span class="badge badge-ghost text-xs">{{ task.due_date }} </span>
                    <span
                        v-if="task.user"
                        class="badge badge-accent text-xs">{{ task.user }} </span>
                    <span
                        v-if="task.assistant"
                        class="badge badge-neutral text-xs">{{ task.assistant }} </span>

                    <div class="flex justify-end gap-2 items-center mx-auto w-full">
                        <button
                            type="button"
                            class="btn btn-circle btn-sm btn-ghost text-secondary" @click="completeTask(task)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<style scoped>

</style>
