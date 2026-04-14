<script setup lang="ts">
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

type ToastTipo = 'sucesso' | 'erro' | 'aviso' | 'info'
const toasts = ref<Array<{ id: number; tipo: ToastTipo; msg: string }>>([])
let toastId = 0

function showToast(tipo: ToastTipo, msg: string) {
    const id = ++toastId
    toasts.value.push({ id, tipo, msg })
    setTimeout(() => {
        toasts.value = toasts.value.filter(t => t.id !== id)
    }, 3500)
}

const page = usePage()

watch(
    () => page.props.flash as { sucesso?: string; erro?: string } | undefined,
    (flash) => {
        if (flash?.sucesso) showToast('sucesso', flash.sucesso)
        if (flash?.erro)    showToast('erro', flash.erro)
    },
    { deep: true }
)

const toastConfig: Record<ToastTipo, { border: string; bg: string; text: string }> = {
    sucesso: { border: '#166534', bg: '#052e16', text: '#86efac' },
    erro:    { border: '#991b1b', bg: '#450a0a', text: '#fca5a5' },
    aviso:   { border: '#854d0e', bg: '#1c1400', text: '#fcd34d' },
    info:    { border: '#1e40af', bg: '#0c1a3d', text: '#93c5fd' },
}

const icons: Record<ToastTipo, string> = {
    sucesso: 'M5 13l4 4L19 7',
    erro:    'M6 18L18 6M6 6l12 12',
    aviso:   'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
    info:    'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
}
</script>

<template>
    <Teleport to="body">
        <div class="fixed top-5 right-5 z-50 flex flex-col gap-2">
            <TransitionGroup
                enter-from-class="opacity-0 translate-y-4"
                enter-active-class="transition duration-300 ease-out"
                leave-to-class="opacity-0 translate-x-4"
                leave-active-class="transition duration-200 ease-in"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="flex items-center gap-3 rounded-lg border px-4 py-3 text-sm shadow-lg min-w-64 max-w-sm"
                    :style="{
                        background:   toastConfig[toast.tipo].bg,
                        borderColor:  toastConfig[toast.tipo].border,
                        color:        toastConfig[toast.tipo].text,
                    }"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icons[toast.tipo]" />
                    </svg>

                    <span class="flex-1">{{ toast.msg }}</span>

                    <button
                        class="opacity-60 hover:opacity-100 transition-opacity"
                        @click="toasts = toasts.filter(t => t.id !== toast.id)"
                    >
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>