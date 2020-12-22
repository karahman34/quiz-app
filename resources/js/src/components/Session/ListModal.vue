<template>
  <div>
    <!-- Main Modal -->
    <modal size="stretch" @hide="$emit('hide')">
      <!-- Header -->
      <template v-slot:header>
        <span class="mdi mdi-book"></span>
        {{ packet.title }}
      </template>

      <div class="mt-2">
        <!-- Alert -->
        <alert
          v-if="alertMessage && alertType"
          class="mt-2 mb-3"
          :type="alertType"
          :message="alertMessage"
          @close="alertMessage = null"
        ></alert>

        <!-- Filter -->
        <div class="flex justify-between items-center gap-3 mt-1 mb-2">
          <!-- Sort & Order -->
          <div class="flex gap-2">
            <select v-model="sort" class="py-1 cursor-pointer rounded border">
              <option value="code">Code</option>
              <option value="status">Status</option>
              <option value="created_at">Created Time</option>
            </select>

            <select v-model="order" class="py-1 cursor-pointer rounded border">
              <option value="asc">ASC</option>
              <option value="desc">DESC</option>
            </select>
          </div>

          <!-- Create Session Button -->
          <div class="flex-shrink-0">
            <my-button
              dark
              color="success"
              class="py-1 uppercase text font-semibold"
              :disabled="onGoingSession"
              @click="createSessionModal = true"
            >
              <span class="mdi mdi-plus"></span>
              create session
            </my-button>
          </div>
        </div>

        <!-- Table -->
        <table class="table-auto w-full border border-gray-400">
          <thead>
            <tr>
              <th class="py-1 bg-gray-200 text-center border border-gray-400">
                Code
              </th>
              <th class="py-1 bg-gray-200 text-center border border-gray-400">
                Participants
              </th>
              <th class="py-1 bg-gray-200 text-center border border-gray-400">
                Available For
              </th>
              <th class="py-1 bg-gray-200 text-center border border-gray-400">
                Status
              </th>
              <th class="py-1 bg-gray-200 text-center border border-gray-400">
                Created At
              </th>
              <th class="py-1 bg-gray-200 text-center border border-gray-400">
                Actions
              </th>
            </tr>
          </thead>

          <tbody>
            <!-- Empty / Loading -->
            <template v-if="loading || !sessions.length">
              <tr>
                <td colspan="6" class="py-2 text-gray-500 text-center">
                  {{ loading ? "Getting data.." : "No Sessions Yet." }}
                </td>
              </tr>
            </template>

            <!-- Real Content -->
            <template v-else>
              <tr v-for="session in sessions" :key="session.id">
                <td class="px-1 text-center border border-gray-400">
                  {{ session.code }}
                </td>

                <td class="px-1 text-center border border-gray-400">
                  <span
                    v-if="session.participants_count === 0"
                    class="font-light text-gray-500"
                  >
                    null
                  </span>

                  <span
                    v-else
                    class="cursor-pointer text-blue-500 font-semibold"
                    @click="
                      (participantsModal = true), (focusSession = session)
                    "
                  >
                    {{ session.participants_count }} Peoples
                  </span>
                </td>

                <td class="px-1 text-center border border-gray-400">
                  {{ session.available_for }} hours
                </td>

                <td class="px-1 text-center border border-gray-400">
                  {{ session.status }}
                </td>

                <td class="px-1 text-center border border-gray-400">
                  {{ createdTime(session.created_at) }}
                </td>

                <td class="px-1 text-center">
                  <!-- Delete -->
                  <span
                    class="mdi mdi-trash-can cursor-pointer text-red-600 text-lg"
                    @click="
                      (deleteSessionModal = true), (focusSession = session)
                    "
                  ></span>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </modal>

    <!-- Create Session Modal -->
    <create-session-modal
      v-if="createSessionModal"
      :packet="packet"
      @created="sessionCreatedHandler"
      @hide="createSessionModal = false"
    ></create-session-modal>

    <!-- Delete Modal -->
    <delete-modal
      v-if="deleteSessionModal"
      :loading="deleteLoading"
      :message="`Are you sure want to delete session with code '${focusSession.code}'`"
      @hide="deleteSessionModal = false"
      @delete="deleteSession"
    ></delete-modal>

    <!-- Participants Modal -->
    <participants-modal
      v-if="participantsModal"
      :packet="packet"
      :session="focusSession"
      @hide="participantsModal = false"
    ></participants-modal>
  </div>
</template>

<script>
import Alert from "../Alert";
import Modal from "../Modal";
import moment from "moment";
import DeleteModal from "../DeleteModal";
import CreateSessionModal from "./CreateModal";
import ParticipantsModal from "./ParticipantsModal";

export default {
  components: {
    Alert,
    Modal,
    CreateSessionModal,
    DeleteModal,
    ParticipantsModal,
  },

  props: {
    packet: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      sessions: [],
      loading: true,
      sort: "created_at",
      order: "desc",
      alertType: null,
      alertMessage: null,
      focusSession: null,
      deleteLoading: false,
      createSessionModal: false,
      deleteSessionModal: false,
      participantsModal: false,
    };
  },

  computed: {
    params() {
      return {
        sort: this.sort,
        order: this.order,
      };
    },
    onGoingSession() {
      const exist = this.sessions.find(
        (session) => session.status === "on_going"
      );

      return exist ? true : false;
    },
  },

  mounted() {
    this.getSessions();
  },

  watch: {
    params: {
      deep: true,
      handler() {
        this.sessions = [];
        this.getSessions();
      },
    },
  },

  methods: {
    createdTime(date) {
      return moment(date).format("llll");
    },
    sessionCreatedHandler() {
      this.sessions = [];
      this.alertType = "success";
      this.alertMessage = "Success to create new session.";
      this.createSessionModal = false;

      if (this.sort === "created_at" && this.order === "desc") {
        this.getSessions();
      } else {
        this.sort = "created_at";
        this.order = "desc";
      }
    },
    async getSessions() {
      this.loading = true;

      try {
        const res = await axios.get(`/packets/${this.packet.id}/sessions`, {
          params: this.params,
        });

        this.sessions = res.data.data;
      } catch (err) {
        this.alertType = "danger";
        this.alertMessage =
          err?.response?.data?.message || "Failed to get sessions data.";
      } finally {
        this.loading = false;
      }
    },
    async deleteSession() {
      this.deleteLoading = true;
      this.alertType = null;
      this.alertMessage = null;

      try {
        await axios.post(`/packets/${this.packet.id}/sessions/delete`, {
          _session_id: this.focusSession.id,
        });

        this.sessions.splice(
          this.sessions.findIndex(
            (session) => session.id === this.focusSession.id
          ),
          1
        );
        this.alertType = "success";
        this.alertMessage = `"${this.focusSession.code}" was successfully deleted.`;
        this.focusSession = null;
      } catch (err) {
        this.alertType = "danger";
        this.alertMessage =
          err?.response?.data?.message ||
          "Failed to delete session, please try again later.";
      } finally {
        this.deleteLoading = false;
        this.deleteSessionModal = false;
      }
    },
  },
};
</script>