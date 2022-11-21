<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chirp from '@/Components/Chirp.vue';
import {useForm, Head} from "@inertiajs/inertia-vue3";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps(['paginated_chirps']);

const form = useForm({
    message: ''
})

function navigate(url) {
    window.location.href = url;
}
</script>

<template>
    <Head title="Chirps"/>
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
            <form @submit.prevent="form.post(route('chirps.store'), {onSuccess: () => form.reset()})">
                <textarea
                    name="chirp-input"
                    v-model="form.message"
                    placeholder="What's new?"
                    class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                ></textarea>
                <InputError :message="form.errors.message" class="mt-2"/>
                <PrimaryButton class="mt-4">Chirp</PrimaryButton>
            </form>
            <div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
                <Chirp
                    v-for="chirp in paginated_chirps.data"
                    :key="chirp.id"
                    :chirp="chirp"
                />
            </div>
            <div class="flex justify-center list-none p-0 mt-8">
                <button v-for="(link,index) in paginated_chirps.links" :key="index"
                        class="px-3 block no-underline text-black rounded-md h-10 border-t-2 border-l-2 border-b-2 disabled:bg-gray-300 disabled:hover:cursor-not-allowed"
                        :class="[
                            index === paginated_chirps.links.length - 1 ? 'border-r-2' : '',
                            link.active ? 'bg-blue-200 hover:bg-blue-300' : 'bg-white hover:bg-gray-200'
                        ]"
                        :disabled="!link.url"
                        @click.prevent="navigate(link.url)"
                >
                    <span v-html="link.label"></span>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
