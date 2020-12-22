<template>
  <modal size="stretch" @hide="$emit('hide')">
    <template v-slot:header>
      <span class="mdi mdi-account-group"></span>
      #{{ session.code }} Participants
    </template>

    <div class="mt-2">
      <!-- Header Filter -->
      <div class="flex items-center mb-2 gap-3">
        <!-- Sort -->
        <select v-model="sort" class="py-1 cursor-pointer rounded border">
          <option value="created_at">Created Time</option>
          <option value="score">Score</option>
          <option value="joined_at">Joined At</option>
          <option value="finished_at">Finished At</option>
        </select>

        <!-- Order -->
        <select v-model="order" class="py-1 cursor-pointer rounded border">
          <option value="asc">ASC</option>
          <option value="desc">DESC</option>
        </select>
      </div>

      <!-- Table -->
      <table class="table-auto w-full border border-gray-400">
        <thead>
          <tr>
            <th class="py-1 bg-gray-200 text-center border border-gray-400">
              Email
            </th>
            <th class="py-1 bg-gray-200 text-center border border-gray-400">
              Username
            </th>
            <th class="py-1 bg-gray-200 text-center border border-gray-400">
              Score
            </th>
            <th class="py-1 bg-gray-200 text-center border border-gray-400">
              Status
            </th>
            <th class="py-1 bg-gray-200 text-center border border-gray-400">
              Joined At
            </th>
            <th class="py-1 bg-gray-200 text-center border border-gray-400">
              Finished At
            </th>
          </tr>
        </thead>

        <tbody>
          <!-- Empty / Loading -->
          <template v-if="loading || !participants.length">
            <tr>
              <td colspan="6" class="py-2 text-gray-500 text-center">
                {{ loading ? "Getting data.." : "No Participants Yet." }}
              </td>
            </tr>
          </template>

          <!-- Real Content -->
          <template v-else>
            <tr v-for="participant in participants" :key="participant.id">
              <td class="px-1 text-center border border-gray-400">
                {{ participant.email }}
              </td>

              <td class="px-1 text-center border border-gray-400">
                {{ participant.username }}
              </td>

              <td class="px-1 text-center border border-gray-400">
                {{ participant.pivot.score }}
              </td>

              <td class="px-1 text-center border border-gray-400">
                {{ participant.pivot.status }}
              </td>

              <td class="px-1 text-center border border-gray-400">
                {{ participant.pivot.joined_at }}
              </td>

              <td class="px-1 text-center border border-gray-400">
                {{ participant.pivot.finished_at }}
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </modal>
</template>

<script>
import Alert from "../Alert";
import Modal from "../Modal";

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
    session: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      participants: [],
      loading: false,
      sort: "joined_at",
      order: "asc",
      alertType: null,
      alertMessage: null,
    };
  },

  computed: {
    params() {
      return {
        sort: this.sort,
        order: this.order,
      };
    },
  },

  watch: {
    params: {
      deep: true,
      handler() {
        this.participants = [];
        this.getParticipants();
      },
    },
  },

  mounted() {
    this.getParticipants();
  },

  methods: {
    async getParticipants() {
      this.loading = true;
      this.alertType = null;
      this.alertMessage = null;

      try {
        const res = await axios.get(
          `/packets/${this.packet.id}/sessions/${this.session.id}/participants`,
          {
            params: this.params,
          }
        );
        const { data } = res.data;

        this.participants = data;
      } catch (err) {
        this.alertType = "danger";
        this.alertMessage =
          err?.response?.data?.message || "Failed to fetch participants data.";
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>