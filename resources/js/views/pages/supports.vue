<template>
  <div id="page-support">
    <b-container>
      <b-row>
        <b-col class="logo-wrapper">
          <img src="/images/logo-new.png" />
        </b-col>
      </b-row>
      <b-row class="mt-5">
        <b-col class="support-header">
          <span>Welcome to the VidGen Support Center</span>
        </b-col>
      </b-row>
      <b-row class="mt-5">
        <b-col md="10" offset="1" v-if="currentUser && currentUser.role === 'publisher'">
          <span class="support-text">
            We are just as excited as you are to start promoting PREMIUM video content from tier 1 brands.<br /><br />
            If for any reason you run into issues regarding advertiser guidelines, placements or anything else that relates to promoting our widget or live links, please contact us as: <br/><br/>
            <strong>
              <a href="mailto:Publishers@vid-GEN.com" class="support-email"
                >Publishers@vid-GEN.com</a>
            </strong>
            <br /><br />
            For technical or reporting issues please skype us at: 
            <br /><br />
            Please allow up to 12 hours for a response.  <br/>
            We look forward to maintaining an extremely mutually rewarding partnership! <br/>
            All the Best, <br/><br/>
            vid-GEN 
          </span>
        </b-col>
        <b-col md="10" offset="1" v-if="currentUser && (currentUser.role === 'advertiser' || currentUser.role === 'origin')">
          <span class="support-text">
            That you for being a valued vid-GEN promotional partner. <br />
            Vid-GEN is a hybrid premium ad network and interactive agency all rolled into one marketplace. We will handle all creative aspects from choosing the right talent, scripting. <br/><br/>
            We also will manage publisher placement, reporting and publisher payments. <br/><br/>
            If for any reason you have any questions or concerns you should contact 
            <strong>
              <a href="mailto:Advertisers@vid-GEN.com" class="support-email"
                >Advertisers@vid-GEN.com</a>
            </strong>.
            <br /><br />
            We will do our best to answer all requests on the same day. <br />
            We look forward to maintaining an extremely mutually rewarding partnership! <br/>
            All the Best, <br/>
            vid-GEN 
          </span>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
$base-color: #3490dc;
#page-support {
  padding: 30px;
  .logo-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    img {
      width: 340px;
    }
  }
  .support-header {
    font-size: 28px;
    background-color: $base-color;
    color: white;
    height: 150px;
    align-content: center;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .support-text {
    color: #000;
    font-size: 18px;
    line-height: 136%;
    letter-spacing: 0px;
    font-weight: 400;
    .support-email {
      color: #e25041;
      text-decoration: none;
      translate: all 0.3s;
      &:hover {
        opacity: 0.75;
      }
    }
  }
}
</style>

<script>
import { mapGetters } from 'vuex';
export default {
  name: 'supports',
  computed: {
    ...mapGetters(['currentUser'])
  },
  beforeMount() {
    if (!this.currentUser || (this.currentUser && this.currentUser.role != 'advertiser' && this.currentUser.role != 'origin' && this.currentUser.role != 'publisher')) {
      this.$func.makeToast(
        this,
        'Error',
        'You are not allowed to visit this page!',
        'danger'
      );
      if (this.currentUser.role == 'admin')
        this.$router.push({path: '/app/admin-payout'});
      else
        this.$router.push({path: '/app/statistics'});
    }
  },
  mounted() {
    console.log(this.currentUser)
  }
};
</script>
