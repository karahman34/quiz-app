<template>
  <div>
    <!-- The Modal -->
    <modal @hide="$emit('hide')">
      <!-- Header -->
      <template v-slot:header>
        <template v-if="isEdit">
          <span class="mdi mdi-pencil"></span>
          <span>Edit</span>
        </template>

        <template v-else>
          <span class="mdi mdi-plus"></span>
          <span>Create</span>
        </template>

        Packet
      </template>

      <!-- The Form -->
      <form @submit.prevent="formSubmitHandler">
        <!-- Title -->
        <div>
          <my-label>Title</my-label>
          <my-input v-model="form.title" type="text" placeholder="Title" />
          <validation-message v-show="errors.title">
            {{ errors.title }}
          </validation-message>
        </div>

        <!-- Lasts For -->
        <div class="mt-3">
          <my-label>Lasts For</my-label>
          <div class="flex items-center gap-3">
            <!-- Hours -->
            <select v-model="form.hours" class="py-1 border rounded">
              <option v-for="hour in 24" :key="hour" :value="leadingZero(hour)">
                {{ hour }} hours
              </option>
            </select>

            <!-- Minutes -->
            <select v-model="form.minutes" class="py-1 border rounded">
              <option value="00">0 minutes</option>
              <option
                v-for="minute in 60"
                :key="minute"
                :value="leadingZero(minute)"
              >
                {{ minute }} minutes
              </option>
              <validation-message v-show="errors.lasts_for">
                {{ errors.lasts_for }}
              </validation-message>
            </select>
          </div>
        </div>

        <!-- Submit -->
        <my-button
          dark
          type="submit"
          class="w-full mt-3 mb-1"
          :loading="loading"
          :disabled="loading"
          >{{ isEdit ? "Update" : "Create" }}</my-button
        >
      </form>
    </modal>
  </div>
</template>

<script>
import axios from "axios";
import Modal from "../Modal";
import Input from "../Form/Input";
import Label from "../Form/Label";
import ValidationMessage from "../Form/ValidationMessage";
import { showValidationErrors, hideValidationErrors } from "../../helpers/form";

export default {
  components: {
    Modal,
    ValidationMessage,
    "my-label": Label,
    "my-input": Input,
  },

  props: {
    packet: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      form: {
        title: "",
        hours: "01",
        minutes: "00",
      },
      errors: {
        title: null,
        lasts_for: null,
      },
      loading: false,
    };
  },

  computed: {
    isEdit() {
      return Object.keys(this.packet).length ? true : false;
    },
  },

  watch: {
    isEdit: {
      immediate: true,
      handler(val) {
        if (val === true) {
          this.form.title = this.packet.title;

          const [hours, minutes] = this.packet.lasts_for.split(":");
          this.form.hours = hours;
          this.form.minutes = minutes;
        }
      },
    },
  },

  methods: {
    leadingZero(number) {
      return parseInt(number) >= 10 ? number : `0${number}`;
    },
    formSubmitHandler() {
      this.loading = true;
      hideValidationErrors(this.errors);

      // Create payload
      const payload = {};
      if (this.isEdit) payload._method = "PATCH";
      payload.title = this.form.title;
      payload.lasts_for = `${this.form.hours}:${this.form.minutes}`;

      return this.isEdit ? this.update(payload) : this.create(payload);
    },
    async create(payload) {
      try {
        const res = await axios.post("/packets", payload);

        this.$emit("created", res.data.data);
      } catch (err) {
        const errCode = err?.response?.status;

        if (errCode === 422) {
          showValidationErrors(this.errors, err.response.data.errors);
        } else {
          alert(err?.response?.data?.message || "Failed to create packet.");
        }
      } finally {
        this.loading = false;
      }
    },
    async update(payload) {
      try {
        const res = await axios.post(`/packets/${this.packet.id}`, payload);

        this.$emit("updated", res.data.data);
      } catch (err) {
        const errCode = err?.response?.status;

        if (errCode === 422) {
          showValidationErrors(this.errors, err.response.data.errors);
        } else {
          alert(err?.response?.data?.message || "Failed to update packet.");
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>