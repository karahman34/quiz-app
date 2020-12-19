<template>
  <div>
    <!-- Create Packet Modal -->
    <packet-form 
      v-if="packetFormModal" :packet="focusPacket" @created="packetCreatedHandler" @updated="packetUpdatedHandler" @hide="packetFormModal = false, focusPacket = {}">
    </packet-form>

    <!-- Delete Modal -->
    <delete-modal
      v-if="deletePacketModal" 
      :loading="loadingDelete"
      :message="`Are you sure want to delete packet with title ${focusPacket.title} ?`" 
      @hide="focusPacket = {}, deletePacketModal = false"
      @delete="deletePacket"
    ></delete-modal>

    <!-- Alert -->
    <alert v-if="alertMessage" :message="alertMessage" @close="alertMessage = null"></alert>

    <!-- Packets Header -->
    <div class="flex flex-wrap justify-between items-center">
      <!-- Title -->
      <div class="text-xl">
          <span class="mdi mdi-notebook-multiple"></span>
          <span class="ml-1">My Packets</span>
      </div>

      <div>
        <!-- Create Button -->
        <a class="bg-indigo-600 h-full rounded py-1 px-3 cursor-pointer text-white text font-bold" @click="packetFormModal = true">
            <span class="mdi mdi-plus"></span>
            <span>Packet</span>
        </a>

        <!-- Search on dekstop -->
        <my-input v-model="search" dense icon="mdi mdi-magnify" class="hidden md:inline-block md:ml-1" placeholder="Search.."></my-input>
      </div>
    </div>

    <!-- Search on mobile -->
    <my-input v-model="search" dense icon="mdi mdi-magnify" class="mt-3 block md:hidden" placeholder="Search.."></my-input>

    <!-- No Packets -->
    <template v-if="!loading && !packets.length">
      <div class="text-center text-xl my-5 text-gray-500">
        You has no packets yet.
      </div>
    </template>

    <!-- List of Packets -->
    <template v-else>
      <div class="my-3 grid gap-4 grid-cols-12">
        <div v-for="packet in packets" :key="packet.id" class="col-span-6 lg:col-span-4">
          <!-- The Packet Feed -->
          <feed :packet="packet" @edit="focusPacket = $event, packetFormModal = true" @delete="deletePacketModal = true, focusPacket=$event"></feed>
        </div>
      </div>
    </template>

    <!-- Get Packets Loading -->
    <template v-if="loading">
      <div class="my-5 text-center text-gray-500">
        <span class="block text-xl mb-1">Getting data...</span>
        <span class="text-3xl mdi mdi-loading mdi-spin"></span>
      </div>
    </template>

    <!-- Load More Button -->
    <my-button v-if="next && !loading" dark class="w-full" @click="getPackets">
      <span class="mdi mdi-eye"></span>
      Load More
    </my-button>
  </div>
</template>

<script>
  import axios from 'axios'
  import PacketForm from './Form'
  import Feed from './Feed'
  import DeleteModal from '../DeleteModal'

  export default {
    components: {
      Feed,
      PacketForm,
      DeleteModal,
    },
    
    data() {
      return {
        // Internal
        packets: [],
        page: 1,
        next: null,
        search: null,
        searchTimeout: null,
        alertMessage: null,
        loading: false,
        loadingDelete: false,
        // Modal
        focusPacket: {},
        packetFormModal: false,
        deletePacketModal: false
      }
    },

    watch: {
      search() {
        if (this.searchTimeout !== null) {
          clearTimeout(this.searchTimeout)
        }

        this.searchTimeout = setTimeout(() => {
          this.page = 1
          this.next = null
          this.packets = []
          this.getPackets()
        }, 400)
      }
    },

    mounted () {
      this.getPackets()
    },

    methods: {
      packetCreatedHandler(newPacket) {
        this.packets.unshift(newPacket)
        this.packetFormModal = false
        this.alertMessage = 'Packet created.'
      },
      packetUpdatedHandler(newPacket) {
        const index = this.packets.findIndex(_packet => _packet.id == newPacket.id)
        this.packets.splice(index, 1, newPacket)
        this.packetFormModal = false
        this.alertMessage = 'Packet updated.'
      },
      async getPackets() {
        this.loading = true

        try {
          const res = await axios.get('/packets', {
            params: {
              page: this.page,
              search: this.search,
              limit: 9,
            }
          })
          const {data, links} = res.data;
          
          this.packets.push(...data)
          
          if (links.next) {
            this.next = true
            this.page += 1
          } else {
            this.next = false
          }
        } catch (err) {
          alert(err?.response?.data?.message || 'Failed to get packets data.')
        } finally {
          this.loading = false
        }
      },
      async deletePacket() {
        this.loadingDelete = true

        try {
          await axios.delete(`/packets/${this.focusPacket.id}`)
          
          this.packets.splice(
            this.packets.findIndex(p => p.id === this.focusPacket.id),
            1,
          )

          this.alertMessage = `"${this.focusPacket.title}" was successfully deleted.`
          this.focusPacket = null
          this.deletePacketModal = false
        } catch (err) {
          alert('Failed to delete packet, please try again later.')
        } finally {
          this.loadingDelete = false
        }
      }
    },
  }
</script>