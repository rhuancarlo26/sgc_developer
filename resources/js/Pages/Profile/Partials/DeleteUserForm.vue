<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import {useForm} from '@inertiajs/vue3';
import {nextTick, ref} from 'vue';

const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmDeleteUserModal = ref(null)

const confirmUserDeletion = () => {
    confirmDeleteUserModal.value.getInstance().show();
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmDeleteUserModal.value.getInstance().hide();
    form.clearErrors()
    form.reset();
};
</script>

<template>
    <div class="card">
        <header class="card-header">
            <div>
                <h2 class="mb-1">Deletar conta</h2>
                <small class="small">
                    Uma vez que sua conta for deletada, todos seus dados serão removidos permanentemente.
                </small>
            </div>
        </header>

        <div class="card-body">
            <div class="d-flex align-items-center">
                <button class="btn btn-danger" @click="confirmUserDeletion">Deletar conta</button>
            </div>
        </div>

    </div>

    <Modal ref="confirmDeleteUserModal">
        <template #body>

            <h2 class="mb-1">
                Você tem certeza de que deseja deletar sua conta?
            </h2>
            <p class="small">
                Uma vez que sua conta for deletada, todos seus dados serão removidos permanentemente.
            </p>

            <div class="mt-5">
                <InputLabel for="password" value="Senha"/>
                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    placeholder="Sua senha"
                    @keyup.enter="deleteUser"
                />
                <InputError :message="form.errors.password" class="mt-2"/>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button class="btn btn-secondary" @click="closeModal"> Cancelar</button>
                <button
                    class="ml-3 btn btn-danger"
                    :disabled="form.processing"
                    @click="deleteUser"
                >
                    Deletar conta
                </button>
            </div>

        </template>
    </Modal>
</template>
