<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clientes',
        href: '/clientes',
    },
    {
        title: 'Novo',
        href: '/clientes/create',
    },
];
</script>

<template>
    <Head title="Novo Cliente" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <Heading
                variant="small"
                title="Novo Cliente"
                description="Cadastre um novo cliente no sistema."
            />

            <Form
                :action="`/clientes`"
                method="post"
                class="mt-6 space-y-6"
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nome">Nome</Label>
                        <Input
                            id="nome"
                            name="nome"
                            type="text"
                            required
                            placeholder="Nome do cliente"
                        />
                        <InputError :message="errors.nome" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="telefone">Telefone</Label>
                        <Input
                            id="telefone"
                            name="telefone"
                            type="text"
                            required
                            placeholder="Telefone"
                        />
                        <InputError :message="errors.telefone" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="email@exemplo.com"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="documento">Documento</Label>
                        <Input
                            id="documento"
                            name="documento"
                            type="text"
                            placeholder="Documento (opcional)"
                        />
                        <InputError :message="errors.documento" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="endereco">Endereço</Label>
                        <Input
                            id="endereco"
                            name="endereco"
                            type="text"
                            placeholder="Endereço (opcional)"
                        />
                        <InputError :message="errors.endereco" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="cidade">Cidade</Label>
                        <Input
                            id="cidade"
                            name="cidade"
                            type="text"
                            placeholder="Cidade (opcional)"
                        />
                        <InputError :message="errors.cidade" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="estado">Estado</Label>
                        <Input
                            id="estado"
                            name="estado"
                            type="text"
                            placeholder="Estado (opcional)"
                        />
                        <InputError :message="errors.estado" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="cep">CEP</Label>
                        <Input
                            id="cep"
                            name="cep"
                            type="text"
                            placeholder="CEP (opcional)"
                        />
                        <InputError :message="errors.cep" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="observacoes">Observações</Label>
                        <textarea
                            id="observacoes"
                            name="observacoes"
                            rows="4"
                            class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                            placeholder="Observações (opcional)"
                        ></textarea>
                        <InputError :message="errors.observacoes" />
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Button type="submit" :disabled="processing">
                        Salvar
                    </Button>
                    <Button as-child variant="secondary" :disabled="processing">
                        <Link :href="`/clientes`">Cancelar</Link>
                    </Button>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>

