<template>
  <div class="requests-item mb-3">
    <div class="d-flex requests-item-content">
      <div class="requests-item-information">
        <div class="fw-700 d-flex">
          <div class="text-break requests-item-information-name" :class="getBannedStatus() ? '' : 'ban-publisher'">{{ item.publisher.name }}</div>
          <div v-if="getBannedStatus()" class="d-flex justify-content-center px-2 requests-item-information-publisher-ban" @click="handlePublisherStatus(0)">
            <a class="d-flex justify-content-center align-items-center"><abbr title="Ban this publisher"><fa-icon :icon="['fas', 'times']" class="text-danger" /></abbr></a>
          </div>
          <div v-else class="d-flex justify-content-center px-2 requests-item-information-publisher-ban" @click="handlePublisherStatus(1)">
            <a class="d-flex justify-content-center align-items-center"><abbr title="Agree this publisher"><fa-icon :icon="['fas', 'check']" class="text-success" /></abbr></a>
          </div>
        </div>
        <div>Avg earn: <span class="fw-700">{{ calcAvgEarn() }}</span></div>
        <div>Total revenue: <span class="fw-700">{{ calcRevenue() }}</span></div>
        <div>Total clicks: <span class="fw-700">{{ item.revenue.length }}</span></div>
        <div class="requests-item-information-date">{{ getFormattedDate(item.publisher.created_at) }}</div>
      </div>
      <div class="requests-item-vidgen text-center">
        <div class="fw-700 requests-item-vidgen-name">{{ item.vidgen.name }}</div>
        <div class="requests-item-vidgen-thumb w-100 text-center mt-2">
          <img :src="item.vidgen.thumb ? getThumbUrl(item.vidgen.thumb) : '/images/logo-new1.png'" @error="defaultImage" />
        </div>
      </div>
      <div class="requests-item-description">
        <div class="text-break text-center fw-700"><a :href="`https://${url}`" target="_blank" class="px-2" v-for="(url,index) in getWebsiteUrl(item.website_url)" :key="index">{{ url }}</a></div>
        <div>{{ item.description }}</div>
      </div>
      <div class="requests-item-status text-center d-flex align-items-center">
        <div class="text-center w-100" :class="getStatusStyle(item.status)"><span class="fw-700">{{ item.status }}</span></div>
      </div>
      <div class="requests-item-action text-center d-flex align-items-center">
        <div class="d-flex flex-column px-3 w-100">
          <button class="btn btn-sm mt-2" :disabled="item.status == 'Approved'" :class="item.status == 'Approved' ? 'btn-secondary' : 'btn-success'" @click="handleStatus('Approved')">
            Approve
          </button>
          <button class="btn btn-sm mt-2" :disabled="item.status == 'Rejected'" :class="item.status == 'Rejected' ? 'btn-secondary' : 'btn-danger'" @click="handleStatus('Rejected')">
            Reject
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
  .requests-item {
    background-color: #eff7fe;
    border-radius: 12px;
    padding: 8px 20px;
    @media (max-width: 768px) {
      .requests-item-content {
        flex-direction: column;
      }
      .requests-item-description {
        padding-left: 0;
        padding-right: 0;
      }
    }
    @media (min-width: 768px) {
      .requests-item-content {
        flex-direction: row;
      }
      .requests-item-description {
        padding-left: 8px;
        padding-right: 8px;
      }
    }
    .fw-700 {
      font-weight: 700;
    }
    .requests-item-vidgen-name {
      max-width: 140px;
    }
    .requests-item-information {
      min-width: 180px;
    }
    .requests-item-information-name {
      max-width: 180px;
    }
    .requests-item-vidgen {
      min-width: 140px;
    }
    .requests-item-status, .requests-item-action {
      min-width: 120px;
    }
    .requests-item-description {
      flex: 1;
    }
    .requests-item-vidgen-thumb img {
      width: 100px;
    }
    .requests-item-information-publisher-ban:hover {
      cursor: pointer;
      opacity: .7;
    }
    .requests-item-information-publisher-ban abbr:hover {
      cursor: pointer;
    }
    .ban-publisher {
      text-decoration-line: line-through;
      text-decoration-color: red;
    }
    .requests-item-information-date {
      // font-size: 12px;
    }
  }
</style>

<script>
export default {
  name: 'requests-item',
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
    getWebsiteUrl(url) {
      return url.split(';');
    },
    getFormattedDate(dateString) {
      const date = new Date(dateString);
      const formattedDate = date.toISOString().replace("T", " ").slice(0, 19);
      return formattedDate;
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
    handleStatus(status) {
      this.item.status = status;
      this.$emit('handleStatus', {id: this.item.id, status: status});
    },
    handlePublisherStatus(status, deleteId = 0) { // status=0: ban, status=1: agree
      let advertiser_id = this.item.creator_id;
      let publisher_id = this.item.publisher_id;
      let item = this.item.publisher_ban.filter(val => val.advertiser_id == advertiser_id && val.publisher_id == publisher_id);
      let del_id = deleteId;
      if (item.length)
        del_id = item[0].id;
      this.$emit('handlePublisherStatus', {advertiser_id: advertiser_id, publisher_id: publisher_id, status: status, deleteId: del_id});
    },
    calcRevenue() {
      return this.item.revenue.reduce((res, val) => res + val.cost, 0);
    },
    calcAvgEarn() {
      let totalRevenue = this.item.revenue.reduce((res, val) => res + val.cost, 0);
      let totalClicks = this.item.revenue.length
      if (totalClicks == 0)
        return 0;
      else
        return parseFloat((totalRevenue / totalClicks).toFixed(2));
    },
    defaultImage(e) {
      e.target.src = '/images/placeholder.png';
    },
    getThumbUrl(url) {
      if (url[0] == 'u') // uploaded file
        return `${this.awsUrl}${url}`;
      return url;
    },
    getBannedStatus() { // true: agreed, false : banned
      let advertiser_id = this.item.creator_id;
      return this.item.publisher_ban.filter(val => val.advertiser_id == advertiser_id).length == 0
    }
  }
};
</script>
