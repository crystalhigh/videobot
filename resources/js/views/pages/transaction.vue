<!-- :singleDatePicker="true" -->
<template>
  <div class="transaction">
    <div class="d-flex align-items-center">
      <b-form-select
        v-model="selectedDateType"
        :options="dateType"
        value-field="type"
        text-field="name"
        class="mr-3"
      />
      <date-range-picker
        ref="picker"
        opens="right"
        :localData="localData"
        :timePicker="false"
        :ranges="false"
        v-model="dateRange"
        :autoApply="true"
        :singleDatePicker="selectedDateType == 0"
        @update="updateDateRange">
        <template v-slot:input="picker" style="min-width: 300px">
          {{ formatDate(picker.startDate) }} -
          {{ formatDate(picker.endDate) }}
          <fa-icon :icon="['fas', 'chevron-down']" class="ml-2" />
        </template>
      </date-range-picker>
    </div>
    <advertiser v-if="currentUser && currentUser.role != 'publisher'" :items_advPaid="items_advPaid" :items_advPending="items_advPending" @handlePage="handlePage" :balance="balance"/>
    <publisher v-else :items_pubEarned="items_pubEarned" :items_pubPaid="items_pubPaid" :items_pubPending="items_pubPending" @handlePage="handlePage" :balance="balance"/>
  </div>
</template>

<style lang="scss" module>
$base-color: #1998e4;
$light-color: #f0f1f3;
  .transaction {
    padding: 30px;
    height: 100%;
    background-color: white;
    border-radius: 30px;
    .reportrange-text {
      background: $light-color;
      border: 1px solid $light-color;
      color: black;
      font-weight: 700;
      padding: 5px 30px;
      border-radius: 30px;
    }
    .custom-select {
      width: 200px;
      background-color: $light-color;
      border: 1px solid $light-color;
      padding: 5px 30px;
      border-radius: 30px;
      font-weight: 700;
      color: black;
      cursor: pointer;
      option {
        padding: 5px 30px;
      }
    }
  }
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import Advertiser from '@/views/pages/transaction/advertiser.vue';
import Publisher from '@/views/pages/transaction/publisher.vue';

export default {
  name: 'transaction',
  computed: {
    ...mapGetters(['currentUser'])
  },
  components: {
    DateRangePicker, Advertiser, Publisher
  },
  watch: {
    selectedDateType: function(newVal, oldVal) {
      if (newVal == 0) { // Daily
        this.dateRange.startDate = `${this.getFormattedDateString()} 00:00:00`;
        this.dateRange.endDate = `${this.getFormattedDateString()} 23:59:59`;
      } else if (newVal == 1) { // Monthly
        this.dateRange.startDate = `${this.getFormattedDateString(null, 1)} 00:00:00`;
        this.dateRange.endDate = `${this.getFormattedDateString(null, 2)} 23:59:59`;
      } else { // Yearly
        let tempDate = new Date();
        this.dateRange.startDate = `${tempDate.getFullYear()}-01-01 00:00:00`;
        this.dateRange.endDate = `${tempDate.getFullYear()}-12-31 23:59:59`;
      }
      this.loadTransactionData();
    }
  },
  data() {
    return {
      localData: {
        firstDay: 1,
        format: 'mm/dd/yyyy',
        direction: 'ltr',
        separator: ' - '
      },
      dateRange: {
        startDate: '',
        endDate: ''
      },
      dateType: [
        { type: 0, name: 'Daily' },
        { type: 1, name: 'Monthly' },
        { type: 2, name: 'Yearly' },
      ],
      selectedDateType: 1,
      items_advPaid: [],
      items_advPending: [],
      items_pubEarned: [],
      items_pubPaid: [],
      items_pubPending: [],
      page: 1,
      total: 0,
      perPage: 10,
      balance: 0,
    };
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
    this.dateRange.startDate = `${this.getFormattedDateString(null, 1)} 00:00:00`;
    this.dateRange.endDate = `${this.getFormattedDateString(null, 2)} 23:59:59`;
  },
  mounted() {
    this.loadTransactionData();
  },
  methods: {
    async loadTransactionData() {
      const loader = this.$loading.show();
      try {
        const res = await axios.get('/api/transaction?start=' + this.dateRange.startDate + '&end=' + this.dateRange.endDate + '&page=' + this.page + '&perPage=' + this.perPage);
        if (res && res.status == 200) {
          this.total = res.data.total;
          this.balance = res.data.balance;
          if (this.currentUser.role == 'publisher') {
            this.items_pubEarned = [];
            this.items_pubPaid = [];
            this.items_pubPending = [];
            res.data.transaction.data.map(val => {
              if (val.metrics_click_count != 0) {
                this.items_pubEarned = [...this.items_pubEarned, {
                  "advertiser_name": val.advertiser.name,
                  "campaign_name": val.vidgen.name,
                  "cost_per_campaign": val.vidgen.cost,
                  "website_url": val.website_url.split(';'),
                  "impressions": val.impression_count,
                  "plays": val.metrics_view_count,
                  "leads": val.metrics_click_count,
                  "sum": val.vidgen.cost * val.metrics_click_count,
                  "period": [val.metrics_first_click_date, val.metrics_last_click_date],
                }];
              }
              if (val.metrics_paid_count != 0) {
                this.items_pubPaid = [...this.items_pubPaid, {
                  "advertiser_name": val.advertiser.name,
                  "campaign_name": val.vidgen.name,
                  "cost_per_campaign": val.vidgen.cost,
                  "website_url": val.website_url.split(';'),
                  "impressions": val.impression_count,
                  "plays": val.metrics_view_count,
                  "leads": val.metrics_paid_count,
                  "sum": val.vidgen.cost * val.metrics_paid_count,
                  "period": [val.metrics_first_click_date, val.metrics_last_click_date],
                }];
              }
              if (val.metrics_pending_count != 0) {
                this.items_pubPending = [...this.items_pubPending, {
                  "advertiser_name": val.advertiser.name,
                  "campaign_name": val.vidgen.name,
                  "cost_per_campaign": val.vidgen.cost,
                  "website_url": val.website_url.split(';'),
                  "impressions": val.impression_count,
                  "plays": val.metrics_view_count,
                  "leads": val.metrics_pending_count,
                  "sum": val.vidgen.cost * val.metrics_pending_count,
                  "period": [val.metrics_first_click_date, val.metrics_last_click_date],
                }];
              }
            });
          } else {
            this.items_advPaid = [];
            this.items_advPending = [];
            res.data.transaction.data.map(val => {
              if (val.metrics_paid_count != 0) {
                this.items_advPaid = [...this.items_advPaid, {
                  "publisher_name": val.publisher.name,
                  "campaign_name": val.vidgen.name,
                  "cost_per_campaign": val.vidgen.cost,
                  "website_url": val.website_url.split(';'),
                  "impressions": val.impression_count,
                  "plays": val.metrics_view_count,
                  "leads": val.metrics_paid_count,
                  "sum": val.vidgen.cost * val.metrics_paid_count,
                  "period": [val.metrics_first_click_date, val.metrics_last_click_date]
                }];
              }
              if (val.metrics_pending_count != 0) {
                this.items_advPending = [...this.items_advPending, {
                  "publisher_name": val.publisher.name,
                  "campaign_name": val.vidgen.name,
                  "cost_per_campaign": val.vidgen.cost,
                  "website_url": val.website_url.split(';'),
                  "impressions": val.impression_count,
                  "plays": val.metrics_view_count,
                  "leads": val.metrics_pending_count,
                  "sum": val.vidgen.cost * val.metrics_pending_count,
                  "period": [val.metrics_first_click_date, val.metrics_last_click_date]
                }];
              }
            });
          }
        }
      } catch (err) {
        console.log('err =>', err);
        this.$func.makeToast(
          this,
          'Error',
          'Cannot load server data!',
          'danger'
        );
      }
      loader && loader.hide();
    },
    handlePage(page) {
      this.page = page;
      this.loadTransactionData();
    },
    getFormattedDate(dateString) {
      const date = new Date(dateString);
      const formattedDate = date.toISOString().replace("T", " ").slice(0, 19);
      return formattedDate;
    },
    formatDate(date) {
      const dt = new Date(date).toLocaleDateString();
      return dt;
    },
    updateDateRange(e) {
      this.dateRange.startDate = `${this.getFormattedDateString(this.dateRange.startDate)} 00:00:00`;
      this.dateRange.endDate = `${this.getFormattedDateString(this.dateRange.endDate)} 23:59:59`;
      this.loadTransactionData();
    },
    getFormattedDateString(date = null, first_last = 0) {
      let tempDate = null;
      if (date)
        tempDate = new Date(date);
      else
        tempDate = new Date();
      let year = tempDate.getFullYear();
      let tempMonth = tempDate.getMonth() + 1;
      let month = tempMonth >= 10 ? tempMonth : '0' + tempMonth;
      let tempDay = 1; // first day
      if (first_last == 0) // param date
        tempDay = tempDate.getDate();
      else if (first_last == 2) // last day
        tempDay = new Date(year, month, 0).getDate();
      let day = tempDay >= 10 ? tempDay : '0' + tempDay;
      return `${year}-${month}-${day}`;
    },
  }
};
</script>
