<template>
    <div class="card">
      <h5 class="card-header">Delete Account</h5>
      <div class="card-body">
        <div class="mb-3 col-12 mb-0">
          <div class="alert alert-warning">
            <h6 class="alert-heading fw-bold mb-1">
              Are you sure you want to delete your account?
            </h6>
            <p class="mb-0">
              Once you delete your account, there is no going back. Please be
              certain.
            </p>
          </div>
        </div>
        <form id="formAccountDeactivation" @submit.prevent="confirmUserDeletion">
          <div class="form-check mb-3">
            <input
              class="form-check-input"
              type="checkbox"
              name="accountActivation"
              id="accountActivation"
              v-model="isChecked"
            />
            <label class="form-check-label" for="accountActivation">
              I confirm my account deactivation
            </label>
          </div>
          <button
            type="submit"
            class="btn btn-danger deactivate-account"
            :disabled="!isChecked"
          >
            Deactivate Account
          </button>
        </form>
      </div>
  
      <!-- Modal -->
      <Modal :show="confirmingUserDeletion" @close="closeModal">
        <div class="p-6">
          <h2 class="text-lg font-medium text-gray-900">
            Are you sure you want to delete your account?
          </h2>
          <p class="mt-1 text-sm text-gray-600">
            Once your account is deleted, all of its resources and data will be
            permanently deleted. Please enter your password to confirm you would
            like to permanently delete your account.
          </p>
          <div class="mt-6">
            <InputLabel for="password" value="Password" class="sr-only" />
            <TextInput
              id="password"
              ref="passwordInput"
              v-model="form.password"
              type="password"
              class="mt-1 block w-3/4"
              placeholder="Password"
              @keyup.enter="deleteUser"
            />
            <InputError :message="form.errors.password" class="mt-2" />
          </div>
          <div class="mt-6 flex justify-end">
            <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
            <DangerButton
              class="ms-3"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="deleteUser"
            >
              Delete Account
            </DangerButton>
          </div>
        </div>
      </Modal>
    </div>
  </template>
  
  <script setup>
  import DangerButton from '@/Components/DangerButton.vue';
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import Modal from '@/Components/Modal.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';
  import { useForm } from '@inertiajs/vue3';
  import { nextTick, ref } from 'vue';
  
  const confirmingUserDeletion = ref(false);
  const passwordInput = ref(null);
  const isChecked = ref(false);
  
  const form = useForm({
    password: '',
  });
  
  const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
  };
  
  const deleteUser = () => {
    form.delete(route('profile.destroy'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
      onError: () => passwordInput.value?.focus(),
      onFinish: () => {
        form.reset();
      },
    });
  };
  
  const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
  };
  </script>
  
  <style scoped>
 
  </style>
  