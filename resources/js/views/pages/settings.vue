<template>
  <div id="page-setting">
    <b-container fluid class="h-100">
      <b-row>
        <b-col class="page-header">
          <h5>Settings</h5>
          <img src="/images/icons/setting.svg" />
        </b-col>
      </b-row>
      <b-row class="mt-3">
        <b-col>
          <b-tabs>
            <b-tab title="Profile" active>
              <setting-profile />
            </b-tab>
            <b-tab
              title="Branding"
              v-if="currentUser && currentUser.role !== 'publisher'"
            >
              <setting-brand />
            </b-tab>
          </b-tabs>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<style lang="scss" moduled>
$base-color: #1998e4;
#page-setting {
  padding: 30px;
  height: 100%;
  background-color: white;
  border-radius: 30px;
  .nav-tabs {
    .nav-link {
      color: #777;
      font-weight: bold;
      &.active,
      &:hover,
      &:focus {
        color: $base-color;
        border-color: #ffffff #ffffff $base-color #ffffff !important;
      }
      &.active {
        border-width: 2px;
      }
    }
  }
}
</style>

<script>
import { mapGetters } from 'vuex';
import SettingProfile from '@/views/pages/settings/profile';
import SettingBrand from '@/views/pages/settings/brand';
export default {
  name: 'settings',
  components: {
    SettingProfile,
    SettingBrand
  },
  computed: {
    ...mapGetters(['currentUser'])
  }
};
</script>
