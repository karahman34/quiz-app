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

        <!-- Submit -->
        <my-button dark type="submit" class="w-full mt-3 mb-1" :loading="loading" :disabled="loading">{{ isEdit ? 'Update' : 'Create' }}</my-button>
      </form>
    </modal>
  </div>
</template>

<script>
  import axios from 'axios'
  import Modal from '../Modal'
  import Input from '../Form/Input'
  import Label from '../Form/Label'
  import ValidationMessage from '../Form/ValidationMessage'
  import {showValidationErrors, hideValidationErrors} from '../../helpers/form'

  export default {
    components: {
      Modal,
      ValidationMessage,
      'my-label': Label,
      'my-input': Input,
    },

    props: {
      packet: {
        type: Object,
        default: null 
      },
    },

    data() {
      return {
        form: {
          title: ''
        },
        errors: {
          title: null
        },
        loading: false
      }
    },

    computed: {
      isEdit() {
        return Object.keys(this.packet).length ? true : false
      }
    },

    watch: {
      isEdit: {
        immediate: true,
        handler(val) {
          if (val === true) {
            this.form.title = this.packet.title
          }
        }
      }
    },

    methods: {
      formSubmitHandler() {
        this.loading = true
        hideValidationErrors(this.errors)

        return this.isEdit ? this.update() : this.create()
      },
      async create() {
        try {
          const res = await axios.post('/packets', this.form)
          
          this.$emit('created', res.data.data)
        } catch (err) {
          const errCode = err?.response?.status

          if (errCode === 422) {
            showValidationErrors(this.errors, err.response.data.errors)
          } else {
            alert(err?.response?.data?.message || 'Failed to create packet.')
          }
        } finally {
          this.loading = false
        }
      },
      async update() {
        try {
          const res = await axios.post(`/packets/${this.packet.id}`, {
            ...this.form,
            _method: 'PATCH'
          })
          
          this.$emit('updated', res.data.data)
        } catch (err) {
          const errCode = err?.response?.status

          if (errCode === 422) {
            showValidationErrors(this.errors, err.response.data.errors)
          } else {
            alert(err?.response?.data?.message || 'Failed to update packet.')
          }
        } finally {
          this.loading = false
        }
      }
    }
  }
</script>