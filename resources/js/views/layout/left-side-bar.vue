<template>
  <div class="left-side-menu">
    <div class="left-stick">
      <router-link to="/app/statistics" tag="a">
        <img src="/images/icons/logo-white-new.svg" class="logo"/>
      </router-link>
      <div class="profile-wrapper">
        <b-dropdown variant="link" no-caret>
          <template #button-content>
            <div class="profile-name">{{ nameAbb }}</div>
          </template>
          <b-dropdown-item href="/app/settings">Settings</b-dropdown-item>
          <b-dropdown-divider></b-dropdown-divider>
          <b-dropdown-item-button @click="signOut"
            >Sign Out</b-dropdown-item-button
          >
        </b-dropdown>
      </div>
    </div>
    <div class="vpop-side-menu-wrapper" :class="!expanded ? 'closed' : ''">
      <div class="menu-profile-name-wrapper">
        <span class="menu-name">Welcome</span>
        <span class="expand-arrow" @click="expandMenu">
          <fa-icon :icon="['fas', 'angle-double-left']" v-if="expanded" />
          <fa-icon :icon="['fas', 'angle-double-right']" v-else />
        </span>
      </div>
      <div class="mt-2">
        <ul class="vpop-side-menu">
          <!-- <router-link
            to="/app/home"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Home"
          >
            <img src="/images/icons/home.svg" />
            <span class="vpop-menu-text">Home</span>
          </router-link> -->
          <router-link
            to="/app/admin-payout"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Admin Payout"
            v-if="currentUser && currentUser.role === 'admin'"
          >
            <img src="/images/icons/payout.svg" />
            <span class="vpop-menu-text">Payout</span>
          </router-link>
          <!-- <router-link
            to="/app/admin-pay-publisher"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Admin Pay Publisher"
            v-if="currentUser && currentUser.role === 'admin'"
          >
            <img src="/images/icons/team.svg" />
            <span class="vpop-menu-text">Pay Publisher</span>
          </router-link> -->
          <router-link
            to="/app/statistics"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Statistics"
            v-if="currentUser && currentUser.role !== 'admin'"
          >
            <img src="/images/icons/stat.svg" />
            <span class="vpop-menu-text">Home</span>
          </router-link>
          <!-- <router-link
            to="/app/ai-video"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="AI Video"
          >
            <img src="/images/videobots/create/desktop-chat.svg" />
            <span class="vpop-menu-text">AI Video</span>
          </router-link> -->
          <router-link
            to="/app/vidgen"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            v-if="currentUser && (currentUser.role == 'advertiser' || currentUser.role == 'origin' || currentUser.role == 'agent')"
            title="Offers"
          >
            <img src="/images/icons/robot.svg" />
            <span class="vpop-menu-text">Creatives</span>
          </router-link>
          <router-link
            to="/app/vidgen-list"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            v-if="currentUser && currentUser.role === 'publisher'"
            title="Offers"
          >
            <img src="/images/icons/robot.svg" />
            <span class="vpop-menu-text">Offers</span>
          </router-link>
          <router-link
            to="/app/requests"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            v-if="currentUser && (currentUser.role == 'advertiser' || currentUser.role == 'origin')"
            title="Requests"
          >
            <img src="/images/icons/requests.svg" />
            <span class="vpop-menu-text">Requests</span>
            <span class="badge bg-danger text-white ml-2" v-if="advRequestsCounts">{{ advRequestsCounts }}</span>
          </router-link>
          <router-link
            to="/app/responses"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            v-if="currentUser && currentUser.role === 'publisher'"
            title="Campaigns"
          >
            <img src="/images/icons/message.svg" />
            <span class="vpop-menu-text">Campaigns</span>
            <span class="badge bg-danger text-white ml-2" v-if="pubResponsesCounts">{{ pubResponsesCounts }}</span>
          </router-link>
          <router-link
            to="/app/transaction"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Transaction"
            v-if="currentUser && (currentUser.role !== 'admin' && currentUser.role !== 'agent')"
          >
            <img src="/images/icons/transaction.svg" />
            <span class="vpop-menu-text">Transaction</span>
          </router-link>
          <router-link
            to="/app/payout"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Payout"
            v-if="currentUser && currentUser.role === 'publisher'"
          >
            <img src="/images/icons/payout.svg" />
            <span class="vpop-menu-text">Payout</span>
          </router-link>
          <!-- <router-link
            to="/app/ai-list"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="VidGen"
            v-if="currentUser && currentUser.role !== 'publisher'"
          >
            <img src="/images/icons/ai-video.svg" />
            <span class="vpop-menu-text">AI Videos</span>
          </router-link> -->
          <router-link
            to="/app/replies"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Replies"
            v-if="currentUser && (currentUser.role == 'advertiser' || currentUser.role == 'origin')"
          >
            <img src="/images/icons/arrow.svg" />
            <span class="vpop-menu-text">Replies</span>
            <span class="badge bg-danger text-white ml-2" v-if="replyCounts">{{ replyCounts }}</span>
          </router-link>
          <router-link
            to="/app/integrations"
            tag="li"
            class="vpop-left-menu-item"
            v-if="currentUser && currentUser.role === 'origin'"
            active-class="vpop-menu-active"
            title="Integrations"
          >
            <img src="/images/icons/integration.svg" />
            <span class="vpop-menu-text">Integrations</span>
          </router-link>
          <!-- <router-link
            to="/app/templates"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            v-if="currentUser && currentUser.role === 'publisher'"
            title="Templates"
          >
            <img src="/images/icons/folder.svg" />
            <span class="vpop-menu-text">Templates</span>
          </router-link> -->

          <router-link
            to="/app/gen-coupon"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Coupons"
            v-if="currentUser && currentUser.template_admin !== 0"
          >
            <img src="/images/icons/coupon.svg" />
            <span class="vpop-menu-text">Coupons</span>
          </router-link>

          <!-- <router-link
            to="/app/trainings"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Training"
            v-if="currentUser && currentUser.role !== 'admin'"
          >
            <img src="/images/icons/training.svg" />
            <span class="vpop-menu-text">Training</span>
          </router-link> -->
          <router-link
            to="/app/members"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Team Members"
            v-if="
              currentUser &&
                (!currentUser.originator || currentUser.originator === '0') &&
                currentUser.role === 'origin'
            "
          >
            <img src="/images/icons/team.svg" />
            <span class="vpop-menu-text">Team Members</span>
          </router-link>
          <!-- <router-link
            to="/app/memberships"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Upgrade your memebership"
            v-if="
              currentUser &&
                (!currentUser.originator || currentUser.originator === '0') &&
                currentUser.role !== 'origin'
            "
          >
            <img src="/images/icons/membership.svg" />
            <span class="vpop-menu-text">Memberships</span>
          </router-link> -->
          <!-- <a
            class="vpop-left-menu-item"
            title="Status"
            href="https://vidpopup.instatus.com/"
            target="_blank"
          >
            <img src="/images/icons/status.svg" />
            <span class="vpop-menu-text">Status</span>
          </a>
          <a
            class="vpop-left-menu-item"
            title="Roadmap"
            href="https://trello.com/b/xl8dJG2s/vidpopup"
            target="_blank"
          >
            <img src="/images/icons/roadmap.svg" />
            <span class="vpop-menu-text">Roadmap</span>
          </a> -->
          <router-link
            to="/app/settings"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Settings"
            v-if="currentUser && currentUser.role !== 'admin'"
          >
            <img src="/images/icons/setting.svg" />
            <span class="vpop-menu-text">Settings</span>
          </router-link>
          <router-link
            to="/app/supports"
            tag="li"
            class="vpop-left-menu-item"
            active-class="vpop-menu-active"
            title="Support"
            v-if="currentUser && (currentUser.role !== 'admin' && currentUser.role !== 'agent')"
          >
            <img src="/images/icons/support.svg" />
            <span class="vpop-menu-text">Support</span>
          </router-link>
        </ul>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
$base-color: #1998e4;
$radius-size: 25px;
.left-side-menu {
  display: flex;
  @media (max-width: 768px) {
    display: none;
  }
  .left-stick {
    background-color: $base-color;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 100vh;
    padding: 20px 10px;
    width: 80px;
    text-align: center;
    .logo {
      width: 70%;
    }
    .profile-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      .profile-name {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        background-color: black;
        justify-content: center;
        color: white;
        border: 2px solid white;
        cursor: pointer;
      }
    }
  }
  .vpop-side-menu-wrapper {
    background-color: white;
    min-height: 100vh;
    width: 220px;
    padding: 20px 0px;
    .menu-profile-name-wrapper {
      display: flex;
      align-items: center;
      justify-content: space-around;
      padding-bottom: 10px;
      border-bottom: 1px solid #ccc;
      span {
        font-size: 1.2rem;
        font-weight: 700;
      }
      a {
        padding: 7px;
        width: 37px;
        height: 37px;
        text-align: center;
        background-color: $base-color;
        border-radius: 50%;
        &:hover {
          filter: brightness(110%);
        }
        img {
          width: 15px;
          margin-bottom: 3px;
          margin-left: 3px;
        }
      }
      .expand-arrow {
        cursor: pointer;
        &:hover {
          opacity: 0.75;
        }
      }
    }
    .vpop-side-menu {
      list-style: none;
      padding-inline-start: 0px;
      margin-top: 30px;
      padding-right: 10px;
      .vpop-left-menu-item {
        width: 100%;
        padding: 15px 30px;
        padding-right: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        color: rgb(33, 37, 41);
        text-decoration: none;
        img {
          width: 25px;
        }
        .vpop-menu-text {
          margin-left: 20px;
        }
      }
      .vpop-menu-active {
        background-color: $base-color;
        color: white;
        position: relative;
        border-top-right-radius: 30px;
        border-bottom-right-radius: 30px;
        img {
          filter: invert(1);
        }
        &::before,
        &::after {
          content: '';
          width: 30px;
          height: 30px;
          background-color: white;
          border-radius: 50%;
          position: absolute;
        }
        &::before {
          top: -30px;
          left: 0px;
          box-shadow: -15px 15px $base-color;
        }
        &::after {
          bottom: -30px;
          left: 0px;
          box-shadow: -15px -15px $base-color;
        }
      }
    }
    &.closed {
      width: 90px;
      .menu-name {
        display: none;
      }
      .vpop-menu-text {
        display: none;
      }
      .vpop-side-menu {
        padding-right: 10px;
        .vpop-left-menu-item {
          padding-left: 30px;
        }
      }
      .vpop-menu-active {
        &::before,
        &::after {
          width: 30px;
          height: 30px;
        }
        &:before {
          top: -30px;
          box-shadow: -15px 15px $base-color;
        }
        &::after {
          bottom: -30px;
          box-shadow: -15px -15px $base-color;
        }
      }
    }
  }
}
</style>

<script>
import { mapGetters } from 'vuex';
import axios from 'axios';
import { LOGOUT } from '@/services/store/auth.module';
import { UNREAD_UPDATED } from '@/services/store/vidpopup.module';
import { ADV_REQUEST_COUNT } from '@/services/store/vidpopup.module';
export default {
  name: 'left-side-bar',
  computed: {
    ...mapGetters(['currentUser', 'unread', 'advReqCount'])
  },
  watch: {
    $route: function(to, from) {
      if (this.userUpdated) {
        return;
      }
      if (to && to.name === 'statistics') {
        this.expanded = true;
      } else {
        this.expanded = false;
      }
    },
    unread: function(n, o) {
      this.replyCounts = n;
    },
    advReqCount: function(n, o) {
      this.advRequestsCounts = n;
    }
  },
  data() {
    return {
      expanded: true,
      nameAbb: '',
      replyCounts: 0,
      advRequestsCounts: 0,
      pubResponsesCounts: 0,
      userUpdated: false
    };
  },
  async mounted() {
    const name = this.currentUser.name.split(' ');
    const namelst = name.map(n => n.charAt(0).toUpperCase());
    this.nameAbb = namelst.join('');
    const [replyCount, adv_pubCount] = await Promise.all([
      axios.get('/api/reply-count'),
      axios.get('/api/adv-pub-count'),
    ])
    .catch(err => {
      console.log('err => ', err)
    })
    .finally(() => {
    });
    this.replyCounts = replyCount.data;
    this.advRequestsCounts = adv_pubCount.data.advertiser;
    this.pubResponsesCounts = adv_pubCount.data.publisher;
    this.$store.dispatch(UNREAD_UPDATED, this.replyCounts);
    this.$store.dispatch(ADV_REQUEST_COUNT, this.advRequestsCounts);
  },
  methods: {
    signOut() {
      this.$store.dispatch(LOGOUT);
      this.$router.push({ name: 'login' }).catch(() => {});
    },
    expandMenu() {
      this.userUpdated = true;
      this.expanded = !this.expanded;
    }
  }
};
</script>
