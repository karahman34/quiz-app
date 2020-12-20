<template>
  <div class="px-4 py-2 bg-white shadow-md rounded">
    <div class="flex">
      <!-- Title -->
      <a :href="`/packets/${packet.id}`" class="font-medium text-lg">{{ packet.title }}</a>

      <!-- Dropdown Menus -->
      <div class="cursor-pointer mr-0 ml-auto">
        <dropdown>
          <!-- Trigger -->
          <template v-slot:trigger="{trigger}">
            <span class="mdi mdi-dots-vertical text-xl" @click="trigger"></span>
          </template>

          <!-- Menus -->
          <template v-slot:menus>
            <!-- Edit -->
            <dropdown-link @click.native="emitEditEvent">
              <span class="mdi mdi-pencil"></span>
              <span>Edit</span>
            </dropdown-link>

            <!-- Delete -->
            <dropdown-link @click.native="emitDeleteEvent">
              <span class="mdi mdi-trash-can"></span>
              <span>Delete</span>
            </dropdown-link>
          </template>
        </dropdown>
      </div>
    </div>

    <!-- Bottom -->
    <div class="flex flex-wrap justify-between items-center text-gray-500">
      <!-- quizzes count -->
      <div class="flex-shrink-0">
        <span class="mdi mdi-book"></span>
        <span>{{ packet.quizzes_count }} Quizzes</span>
      </div>

      <!-- Time Created -->
      <div>
        <span class="mdi mdi-calendar"></span>
        <span>{{ createdTime }}</span>
      </div>
    </div>
  </div>
</template>

<script>
  import moment from 'moment'

  export default {
    props: {
      packet: {
        type: Object,
        required: true
      },
    },

    computed: {
      createdTime() {
        return moment(this.packet.created_at).fromNow() 
      }
    },

    methods: {
      emitEditEvent() {
        this.$emit('edit', this.packet)
      },
      emitDeleteEvent() {
        this.$emit('delete', this.packet)
      }
    },
  }
</script>