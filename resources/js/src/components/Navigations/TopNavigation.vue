<template>
  <nav class="bg-white text-gray-800 flex items-center">
    <div
      class="w-full flex items-center justify-between max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
    >
      <!-- Logo Banner -->
      <a href="/dashboard" id="logo-banner" class="text-2xl text-indigo-500"
        >QuizApp</a
      >

      <!-- List Menus - desktop -->
      <div class="hidden flex items-center gap-x-5 md:block">
        <!-- User Dropdown -->
        <dropdown>
          <!-- Trigger -->
          <template v-slot:trigger="{ trigger }">
            <div class="cursor-pointer" @click="trigger">
              <span>{{ auth.username }}</span>
              <span class="mdi mdi-chevron-down mdi-18px"></span>
            </div>
          </template>

          <!-- Menus -->
          <template v-slot:menus>
            <dropdown-link class="logout-button">Logout</dropdown-link>
          </template>
        </dropdown>
      </div>

      <!-- List Menus - mobile -->
      <div class="relative block md:hidden">
        <!-- Trigger -->
        <span
          id="mobile-menu-trigger"
          class="mdi mdi-24px cursor-pointer"
          :class="[showDrawer ? 'mdi-close' : 'mdi-menu']"
          @click="showDrawer = !showDrawer"
        ></span>

        <!-- Drawer -->
        <div id="drawer" :class="{ 'is-hidden': !showDrawer }">
          <div id="drawer-menus">
            <!-- Dashboard -->
            <a href="/dashboard" class="menu">
              <span class="menu-icon mdi mdi-home mdi-18px"></span>
              <span class="menu-link">Dashboard</span>
            </a>

            <!-- Join Session -->
            <a href="/sessions/join" class="menu">
              <span class="menu-icon mdi mdi-share mdi-18px"></span>
              <span class="menu-link">Join Session</span>
            </a>

            <!-- Logout -->
            <a href="#" class="menu logout-button">
              <span class="menu-icon mdi mdi-logout mdi-18px"></span>
              <span class="menu-link">
                <span>Logout </span>
                <span class="text-gray-600">-- @{{ auth.username }}</span>
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import DropdownLink from "../Form/DropdownLink.vue";

export default {
  components: { DropdownLink },
  props: {
    auth: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      showDrawer: false,
      openUserSubMenus: false,
    };
  },

  mounted() {
    this.setActiveMenu();
    document.addEventListener("click", this.clickOutsideHandler);
  },

  methods: {
    setActiveMenu() {
      const menus = document.querySelectorAll("#drawer #drawer-menus .menu");
      for (let i = 0; i < menus.length; i++) {
        const menu = menus[i];

        if (menu.href === window.location.href) {
          menu.classList.add("active");
          return;
        }
      }
    },
    clickOutsideHandler(e) {
      const target = e.target;
      const exluded = [
        document.querySelector("#mobile-menu-trigger"),
        document.querySelector("#drawer #drawer-menus"),
        ...document
          .querySelector("#drawer #drawer-menus")
          .querySelectorAll("*"),
      ];

      for (let i = 0; i < exluded.length; i++) {
        const el = exluded[i];
        if (el.isSameNode(target)) {
          return false;
        }
      }

      this.showDrawer = false;
    },
  },

  beforeDestroy() {
    document.removeEventListener("click", this.clickOutsideHandler);
  },
};
</script>

<style lang="scss" scoped>
$nav-height: 61px;

nav {
  height: $nav-height;
}

#logo-banner {
  font-family: "Pacifico", cursive;
}

#drawer {
  z-index: 9999;
  position: fixed;
  top: $nav-height;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  transition: background-color 350ms;

  #drawer-menus {
    padding: 5px 0;
    position: relative;
    left: 0;
    transition: left 600ms;
    max-height: 60vh;
    overflow-y: auto;
    background-color: white;

    .menu {
      cursor: pointer;
      display: flex;
      align-items: center;
      padding: 0.5rem 1rem;
      font-size: 1.05rem;

      &:hover {
        background-color: rgb(228, 226, 226);
      }

      .menu-icon {
        margin-right: 0.5rem;
      }
    }

    .menu.active {
      color: rgb(99, 102, 241);
    }
  }
}

#drawer.is-hidden {
  z-index: -1;
  top: -100%;
  background-color: transparent;

  #drawer-menus {
    left: 100%;
  }
}
</style>