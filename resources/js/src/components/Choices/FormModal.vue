<template>
  <modal @hide="emitHideEvent">
    <!-- Header -->
    <template v-slot:header>
      <span class="mdi mdi-form-select"></span>
      {{ isEdit ? "Edit" : "Create" }} Choice
    </template>

    <!-- The Form -->
    <form @submit.prevent="formSubmitHandler">
      <!-- Text -->
      <div>
        <my-label>Text</my-label>
        <my-input v-model="form.text" dense placeholder="Text"></my-input>
        <validation-message v-if="errors.text">
          {{ errors.text }}
        </validation-message>
      </div>

      <!-- Image -->
      <div class="mt-2">
        <my-label>Image</my-label>
        <!-- Preview -->
        <template v-if="previewImage">
          <img
            :src="previewImage"
            class="mb-2 max-h-96 w-full object-cover"
            alt="Preview Image"
          />
        </template>
        <!-- File Input -->
        <my-file-input accept="image/*" @change="imageUploadHander">
          <template v-slot:append="{ remove }">
            <!-- Remove Preview -->
            <span
              v-if="previewImage"
              class="cursor-pointer block mt-1 underline"
              @click="removeImage(), remove()"
            >
              <span class="mdi mdi-close"></span>
              Remove Image
            </span>
          </template>
        </my-file-input>
      </div>

      <!-- Bottom -->
      <div class="flex justify-end gap-3 mt-3">
        <!-- Close -->
        <my-button color="light" @click="emitHideEvent">Close</my-button>

        <!-- Submit -->
        <my-button dark type="submit" color="primary" :disabled="loading">{{
          isEdit ? "Update" : "Create"
        }}</my-button>
      </div>
    </form>
  </modal>
</template>

<script>
import { hideValidationErrors, showValidationErrors } from "../../helpers/form";
import ValidationMessage from "../Form/ValidationMessage";
import Modal from "../Modal";

export default {
  components: {
    Modal,
    ValidationMessage,
  },

  props: {
    packetId: {
      type: Number,
      required: true,
    },
    quizId: {
      type: Number,
      required: true,
    },
    choice: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      form: {
        text: "",
        image: null,
        old_image_deleted: null,
      },
      errors: {
        text: null,
      },
      loading: false,
      previewImage: null,
    };
  },

  computed: {
    isEdit() {
      return Object.keys(this.choice).length ? true : false;
    },
  },

  mounted() {
    if (this.isEdit) {
      const { text, image } = this.choice;

      this.form.text = text;
      this.form.old_image_deleted = false;
      if (image) this.previewImage = image.url;
    }
  },

  methods: {
    emitHideEvent() {
      this.$emit("hide");
    },
    removeImage() {
      this.form.image = null;
      this.previewImage = null;

      if (this.form.old_image_deleted !== null) {
        this.form.old_image_deleted = true;
      }
    },
    imageUploadHander(fileImage) {
      if (!fileImage) {
        this.previewImage = null;
        this.form.image = null;

        if (this.form.old_image_deleted !== null) {
          this.form.old_image_deleted = true;
        }
      } else {
        this.previewImage = URL.createObjectURL(fileImage);
        this.form.image = fileImage;
      }
    },
    formSubmitHandler() {
      this.loading = true;
      hideValidationErrors(this.errors);

      const { text, image } = this.form;
      const payload = new FormData();

      if (text && text.length) payload.append("text", text);
      if (image) payload.append("image", image);
      payload.append("_packet_id", this.packetId);
      payload.append("_quiz_id", this.quizId);

      if (this.isEdit) {
        payload.append("_method", "patch");

        if (this.form.old_image_deleted === true)
          payload.append("old_image_deleted", "y");
      }

      return this.isEdit ? this.update(payload) : this.create(payload);
    },
    async create(payload) {
      try {
        const { data } = await axios.post("/choices", payload);

        this.$emit("created", data.data);
      } catch (err) {
        const errCode = err?.response?.status;

        if (errCode === 422) {
          showValidationErrors(this.errors, err.response.data.errors);
        } else {
          alert("Failed to create choice, please try again later.");
        }
      } finally {
        this.loading = false;
      }
    },
    async update(payload) {
      try {
        const { data } = await axios.post(
          `/choices/${this.choice.id}`,
          payload
        );

        this.$emit("updated", data.data);
      } catch (err) {
        const errCode = err?.response?.status;

        if (errCode === 422) {
          showValidationErrors(this.errors, err.response.data.errors);
        } else {
          alert("Failed to update choice, please try again later.");
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
