<template>
  <div id="page-vidpopup-screen">
    <b-container>
      <screen-recorder @onRecordSuccess="handleSuccess" @onBack="handleBack" />
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
#page-vidpopup-screen {
  height: 100%;
  padding: 30px;
  border-radius: 30px;
  display: flex;
  justify-content: center;
  position: relative;
  overflow: hidden;
}
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
import ScreenRecorder from '@/views/components/screen-recorder';
export default {
  name: 'vidpopup-screen',
  components: {
    ScreenRecorder
  },
  data() {
    return {
      id: '',
      step_id: '',
      index: -1
    };
  },
  computed: {
    ...mapGetters(['currentUser'])
  },
  beforeMount() {
    if (!this.currentUser || (this.currentUser && this.currentUser.role != 'agent')) {
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
  async mounted() {
    this.id = this.$route.params.id;
    this.step_id = this.$route.params.step;
    this.index = this.$route.query.index;
  },
  methods: {
    handleSuccess(info) {
      const loader = this.$loading.show();
      const formData = new FormData();
      formData.append('file', info.data);
      formData.append('vid', this.id);
      formData.append('fileType', 'camera');
      formData.append('sid', this.step_id);
      formData.append('index', this.index);
      if (window.stream) {
        window.stream.getTracks().forEach(function(track) {
          track.stop();
        });
      }
      axios
        .post('/api/create-step', formData)
        .then(res => {
          if (res.status === 201) {
            loader && loader.hide();

            this.$router.push({
              path: `/app/vidgen/${this.id}/${res.data.id}/edit/overlay`
            });
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot create step right now!',
              'danger'
            );
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot create step right now!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    handleBack() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.step_id}/manage?index=${this.index}`
      });
    }
  }
};
</script>
