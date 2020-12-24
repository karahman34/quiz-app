<template>
  <div>
    <!-- Alert -->
    <alert
      v-if="alertType && alertMessage"
      class="mb-2"
      :type="alertType"
      :message="alertMessage"
      @close="alertMessage = null"
    ></alert>

    <!-- Header -->
    <div class="text-xl mb-2">
      <span class="mdi mdi-history"></span>
      <span>Activities</span>
    </div>

    <!-- Loading -->
    <template v-if="loading">
      <div class="flex flex-col items-center gap-3 text-xl text-gray-500">
        <span>Getting activities data...</span>
        <span class="mdi mdi-loading mdi-spin text-3xl"></span>
      </div>
    </template>

    <!-- Empty Activities -->
    <template v-else-if="!activities.length">
      <div class="text-xl text-gray-500">No activities yet.</div>
    </template>

    <!-- List of Activities -->
    <template v-else>
      <ul class="list-disc">
        <li v-for="activity in activities" :key="activity.id" class="ml-6">
          <div class="flex items-start justify-between">
            <!-- Detail -->
            <div>
              <!-- Text -->
              <span>{{ activity.message }}</span>

              <!-- Time -->
              <span class="ml-1 text-gray-500">{{
                formatActivity(activity.created_at)
              }}</span>
            </div>

            <!-- Actions -->
            <div class="flex-shrink-0">
              <!-- Delete -->
              <span
                class="mdi mdi-trash-can text-xl cursor-pointer text-gray-600"
                @click="(focusActivity = activity), (deleteModal = true)"
              ></span>
            </div>
          </div>
        </li>
      </ul>
    </template>

    <!-- Delete Modal -->
    <delete-modal
      v-if="deleteModal"
      :loading="loadingDelete"
      :message="`Are you sure want to delete '${focusActivity.message}'`"
      @hide="deleteModal = false"
      @delete="deleteActivity"
    ></delete-modal>
  </div>
</template>

<script>
import moment from "moment";
import Alert from "../Alert";
import DeleteModal from "../DeleteModal";

export default {
  components: {
    Alert,
    DeleteModal,
  },

  data() {
    return {
      activities: [],
      loading: true,
      next: null,
      page: 1,
      // Delete
      alertType: null,
      alertMessage: null,
      loadingDelete: false,
      focusActivity: null,
      deleteModal: false,
    };
  },

  mounted() {
    this.getActivities();
  },

  methods: {
    formatActivity(date) {
      return moment(date).calendar();
    },
    async getActivities() {
      this.loading = true;

      try {
        const res = await axios.get("/activities", {
          params: {
            page: this.page,
          },
        });
        const { data, links } = res.data;

        this.activities.push(...data);
        this.page += 1;
        this.next = links.next ? true : false;
      } catch (err) {
        alert(err?.response?.data?.message || "Failed to get activities data.");
      } finally {
        this.loading = false;
      }
    },
    async deleteActivity() {
      this.loadingDelete = true;

      try {
        await axios.delete(`/activities/${this.focusActivity.id}`);

        this.activities.splice(
          this.activities.findIndex(
            (activity) => activity.id === this.focusActivity.id
          ),
          1
        );
        this.alertType = "success";
        this.alertMessage = "One activity deleted.";
      } catch (err) {
        this.alertType = "danger";
        this.alertMessage =
          "Failed to delete activity, please try again later.";
      } finally {
        this.deleteModal = false;
        this.loadingDelete = false;
      }
    },
  },
};
</script>