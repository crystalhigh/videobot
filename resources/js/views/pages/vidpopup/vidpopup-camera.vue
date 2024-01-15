<template>
  <div id="page-vidpopup-camera">
    <b-container>
      <camera-recorder
        @onRecordSuccess="handleSuccess"
        @onBack="handleBack"
      />
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
#page-vidpopup-camera {
  height: 100%;
  border-radius: 30px;
  display: flex;
  justify-content: center;
  position: relative;
  overflow: hidden;
}
</style>

<script>
import axios from 'axios';
import CameraRecorder from '@/views/components/camera-recorder';
export default {
  name: 'vidpopup-camera',
  components: {
    CameraRecorder
  },
  data() {
    return {
      id: '',
      step_id: '',
      index: -1
    };
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
  mounted() {
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
      axios
        .post('/api/create-step', formData)
        .then(res => {
          if (res.status === 201) {
            loader && loader.hide();
            if (window.stream) {
              window.stream.getTracks().forEach(function(track) {
                track.stop();
              });
            }
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
