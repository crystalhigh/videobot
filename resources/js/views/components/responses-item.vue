<template>
  <div class="responses-item mb-3">
    <div class="d-flex responses-item-content">
      <div class="responses-item-vidgen text-center">
        <div class="fw-700 responses-item-vidgen-name">{{ item.vidgen.name }}</div>
        <div class="text-break fw-700 responses-item-vidgen-name">{{ item.advertiser.name }}</div>
        <div class="responses-item-vidgen-thumb w-100 text-center mt-2">
          <img :src="item.vidgen.thumb ? getThumbUrl(item.vidgen.thumb) : '/images/logo-new1.png'" @error="defaultImage" />
        </div>
        <div class="responses-item-vidgen-date mt-2">{{ getFormattedDate(item.created_at) }}</div>
      </div>
      <div class="responses-item-description">
        <div class="text-break text-center fw-700">{{ item.website_url }}</div>
        <div>{{ item.description }}</div>
      </div>
      <div class="responses-item-status text-center d-flex align-items-center flex-column justify-content-center">
        <div class="text-center w-100" :class="getStatusStyle(item.status)"><span class="fw-700">{{ item.status }}</span></div>
        <div class="responses-item-vidgen-date mt-2">{{ getFormattedDate(item.updated_at) }}</div>
      </div>
      <div class="responses-item-tools text-center d-flex flex-column">
        <div class="fw-700">Promotional Tools</div>
        <div class="d-flex flex-column px-3 w-100 responses-item-tools-button justify-content-center">
          <button class="btn btn-sm mt-2" :class="item.status == 'Approved' ? 'btn-success' : 'btn-secondary'" :disabled="item.status != 'Approved'" @click="onPromoteTools()">
            Promotional Tools
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
  .responses-item {
    background-color: #eff7fe;
    border-radius: 12px;
    padding: 8px 20px;
    @media (max-width: 768px) {
      .responses-item-content {
        flex-direction: column;
      }
      .responses-item-description {
        padding-left: 0;
        padding-right: 0;
      }
    }
    @media (min-width: 768px) {
      .responses-item-content {
        flex-direction: row;
      }
      .responses-item-description {
        padding-left: 8px;
        padding-right: 8px;
      }
    }
    .responses-item-vidgen {
      min-width: 160px;
    }
    .responses-item-vidgen-name {
      max-width: 160px;
    }
    .responses-item-tools {
      min-width: 140px;
    }
    .responses-item-vidgen-thumb img {
      width: 100px;
    }
    .responses-item-description {
      flex: 1;
    }
    .responses-item-status {
      min-width: 120px;
    }
    .fw-700 {
      font-weight: 700;
    }
    .responses-item-vidgen-date {
      font-size: 10px;
    }
    .responses-item-tools-button {
      flex: 1;
    }
  }
</style>

<script>
export default {
  name: 'responses-item',
  props: {
    item: { type: Object, default: {} },
  },
  data() {
    return {
      awsUrl: 'https://vidpopup.s3.amazonaws.com/',
    };
  },
  mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;
  },
  methods: {
    getThumbUrl(url) {
      if (url[0] == 'u') // uploaded file
        return `${this.awsUrl}${url}`;
      return url;
    },
    getStatusStyle(status) {
      if (status == 'Pending')
        return 'text-secondary';
      else if (status == 'Approved')
        return 'text-success';
      else if (status == 'Rejected')
        return 'text-danger';
      else
        return 'text-secondary';
    },
    defaultImage(e) {
      e.target.src = '/images/placeholder.png';
    },
    getFormattedDate(dateString) {
      const date = new Date(dateString);
      const formattedDate = date.toISOString().replace("T", " ").slice(0, 19);
      return formattedDate;
    },
    onPromoteTools() {
      this.$emit('handleCopy', {
        vidgenCode: this.item.vidgen.code, 
        pv_id: this.item.id,
        thumb: this.item.steps.length ? this.item.steps.filter(val => val.index == 1)[0].video_url : '',
        widget: this.item.vidgen.widget
      });
    }
  }
};
</script>
