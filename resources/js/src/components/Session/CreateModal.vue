<template>
  <modal @hide="$emit('hide')">
    <template v-slot:header>
      <span class="mdi mdi-plus"></span>
      Create Session
    </template>

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

export default {
  components: {
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
    };
  },

  methods: {
    async createSession() {
      this.loading = true;

      try {
        const res = await axios.post(
          `/packets/${this.packet.id}/sessions`,
          this.form
        );
        const { data } = res.data;

        this.$emit("created", data);
      } catch (err) {
        alert(
          err?.response?.data?.message ||
            "Failed to create session, please try again later."
        );
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>