<template>
  <modal @hide="$emit('hide')" class="text-lg">
    <div class="flex flex-col gap-4 items-center">
      <!-- Icon -->
      <div class="flex justify-center">
        <span class="mdi mdi-alert-circle text-6xl text-yellow-500"></span>
      </div>

      <!-- Message Text -->
      <span>{{ message }}</span>

      <div class="flex justify-evenly gap-5 mb-2">
        <!-- Close -->
        <my-button color="light" class="hover:bg-gray-400" @click.native="$emit('hide')">Close</my-button>

        <!-- Delete -->
        <my-button dark color="dark" class="hover:bg-red-500" :loading="loading" @click.native="emitDeleteEvent">Delete</my-button>
      </div>
    </div>
  </modal>
</template>

<script>
  import Modal from './Modal.vue'

  export default {
    components: {
      Modal,
    },
    
    props: {
      message: {
        type: String,
        required: true,
      },
      loading: {
        type: Boolean,
        required: true
      },
    },

    methods: {
      emitDeleteEvent() {
        return this.$emit('delete')
      }
    },
  }
</script>