<template>
  <modal @hide="$emit('hide')">
    <template v-slot:header>
      <span class="mdi mdi-plus"></span>
      Create Session
    </template>

    <!-- Alert -->
    <alert
      v-if="alertMessage"
      class="my-2"
      type="danger"
      :message="alertMessage"
      @close="alertMessage = null"
    ></alert>

    <!-- The Form -->
    <form @submit.prevent="createSession">
      <!-- Packet -->
      <div>
        <my-label>Packet</my-label>
        <my-input :value="packet.title" readonly></my-input>
      </div>

      <!-- Available For -->
      <div class="mt-2">
        <my-label>Available For</my-label>
        <select
          v-model="form.available_for"
          class="py-1 cursor-pointer rounded"
        >
          <option v-for="hour in 24" :key="hour" :value="hour">
            {{ hour }} hour
          </option>
        </select>
      </div>

      <div class="flex justify-between mt-4">
        <!-- Close -->
        <my-button color="light" @click="$emit('hide')">Close</my-button>

        <!-- Submit -->
        <my-button dark type="submit" :loading="loading">Submit</my-button>
      </div>
    </form>
  </modal>
</template>

<script>
import Modal from "../Modal";
import Alert from "../Alert";

export default {
  components: {
    Alert,
    Modal,
  },

  props: {
    packet: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      form: {
        available_for: 1,
      },
      loading: false,
      alertMessage: null,
    };
  },

  methods: {
    async createSession() {
      this.loading = true;
      this.alertMessage = null;

      try {
        const res = await axios.post(
          `/packets/${this.packet.id}/sessions`,
          this.form
        );
        const { data } = res.data;

        this.$emit("created", data);
      } catch (err) {
        this.alertMessage =
          err?.response?.data?.message ||
          "Failed to create session, please try again later.";
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>