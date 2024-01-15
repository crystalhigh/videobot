<template>
  <div id="page-stat">
    <b-container fluid class="h-100">
      <div class="d-flex justify-content-between">
        <div class="page-header">
          <h5 class="mb00">Statistics</h5>
          <img src="/images/icons/stat.svg" />
        </div>
        <h5
          :class="s3Storage < 1025 ? 'text-danger' : 'text-primary'"
          v-if="showStorage"
        >
          {{ storageLeft }}
        </h5>
      </div>
      <b-row class="mt-4">
        <b-col class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <date-range-picker
              ref="picker"
              opens="center"
              :localData="localData"
              :minDate="minDate"
              :maxDate="maxDate"
              :timePicker="false"
              :ranges="false"
              v-model="dateRange"
              :autoApply="true"
              @update="updateRange"
            >
              <template v-slot:input="picker" style="min-width: 300px">
                {{ formatDate(picker.startDate) }} -
                {{ formatDate(picker.endDate) }}
                <fa-icon :icon="['fas', 'chevron-down']" class="ml-2" />
              </template>
            </date-range-picker>
            <b-form-select
              v-model="selectedVid"
              :options="vidpops"
              value-field="vidpop_id"
              text-field="name"
              class="ml-3"
            />
          </div>
          <router-link to="/app/vidgen/create" class="create-vidpop" v-if="currentUser && currentUser.role == 'agent'">
            <span>Create VidGen</span>
            <div class="img-wrapper">
              <img src="/images/icons/create.svg" />
            </div>
          </router-link>
        </b-col>
      </b-row>
      <b-row class="mt-5">
        <b-col :class="(currentUser && currentUser.role == 'publisher') ? 'col-flex-5' : 'col-md-3'">
          <div
            class="stat-card"
            :class="activeCard === 'impression' ? 'active' : ''"
            @click="onSelectCard('impression')"
          >
            <div class="stat-inner">
              <!-- <h3>{{ impressions }}</h3> -->
              <h3>{{ (currentUser && currentUser.role == 'publisher') ? pubTotalCampaigns : advTotalCampaigns }}</h3>
              <label>
                Total Campaigns
              </label>
            </div>
          </div>
        </b-col>
        <b-col :class="(currentUser && currentUser.role == 'publisher') ? 'col-flex-5' : 'col-md-3'">
          <div
            class="stat-card"
            :class="activeCard === 'reply' ? 'active' : ''"
            @click="onSelectCard('reply')"
          >
            <div class="stat-inner">
              <!-- <h3>{{ replies }}</h3> -->
              <h3>{{ (currentUser && currentUser.role == 'publisher') ? pubVideoPlays : advVideoPlays }}</h3>
              <label>
                Video Plays
              </label>
            </div>
          </div>
        </b-col>
        <b-col :class="(currentUser && currentUser.role == 'publisher') ? 'col-flex-5' : 'col-md-3'">
          <div
            class="stat-card"
            :class="activeCard === 'total' ? 'active' : ''"
            @click="onSelectCard('total')"
          >
            <div class="stat-inner">
              <h3>{{ (currentUser && currentUser.role == 'publisher') ? pubLeads : advLeads }}</h3>
              <label>
                Leads
              </label>
            </div>
          </div>
        </b-col>
        <b-col :class="(currentUser && currentUser.role == 'publisher') ? 'col-flex-5' : 'col-md-3'">
          <div
            class="stat-card stat-rate"
            :class="activeCard === 'rate' ? 'active' : ''"
            @click="onSelectCard('rate')"
          >
            <div class="stat-inner">
              <h3>{{ (currentUser && currentUser.role == 'publisher') ? pubAvgViewRate : advAvgViewRate }}%</h3>
              <label>
                Avg. view rate
              </label>
            </div>
          </div>
        </b-col>
        <b-col :class="(currentUser && currentUser.role == 'publisher') ? 'col-flex-5' : 'col-md-3'" v-if="currentUser && currentUser.role === 'publisher'">
          <div
            class="stat-card stat-rate"
            :class="activeCard === 'rate' ? 'active' : ''"
            @click="onSelectCard('rate')"
          >
            <div class="stat-inner">
              <h3> ${{ pubRevenue }} <span class="ml-1"></span></h3>
              <label>
                Revenue
              </label>
            </div>
          </div>
        </b-col>
      </b-row>
      <b-row class="mt-4" v-if="currentUser && currentUser.role !== 'publisher'">
        <b-col md="3">
          <div
            class="stat-card"
            :class="activeCard === 'approved' ? 'active' : ''"
            @click="onSelectCard('impression')"
          >
            <div class="stat-inner">
              <h3>{{ advApproved }}</h3>
              <label>
                Total Approved Publishers
              </label>
            </div>
          </div>
        </b-col>
        <b-col md="3">
          <div
            class="stat-card"
            :class="activeCard === 'spend' ? 'active' : ''"
            @click="onSelectCard('reply')"
          >
            <div class="stat-inner">
              <h3>${{ advSpend }}</h3>
              <label>
                Spend
              </label>
            </div>
          </div>
        </b-col>
        <b-col md="3">
          <div
            class="stat-card"
            :class="activeCard === 'cost' ? 'active' : ''"
            @click="onSelectCard('total')"
          >
            <div class="stat-inner">
              <h3>${{ advCost }}</h3>
              <label>
                Cost Per Lead
              </label>
            </div>
          </div>
        </b-col>
        <b-col md="3">
          <div
            class="stat-card"
            :class="activeCard === 'balance' ? 'active' : ''"
            @click="onSelectCard('total')"
          >
            <div class="stat-inner">
              <h3>${{ advBalance }}</h3>
              <label>
                AD Balance
              </label>
              <div>
                <button class="btn btn-sm btn-success btn-account-top-up px-3" @click="onAccountTopUp" v-if="currentUser && currentUser.role === 'advertiser'">Top Up</button>
              </div>
            </div>
          </div>
        </b-col>
      </b-row>
      <b-row class="mt-5">
        <b-col>
          <div class="chart-wrapper">
            <VueApexCharts
              type="line"
              height="360"
              :options="options"
              :series="series"
              style="width: 100%"
            ></VueApexCharts>
          </div>
        </b-col>
      </b-row>
      <b-row class="mt-5" v-if="currentUser && currentUser.role === 'publisher'">
        <b-table
          hover
          bordered
          sticky-header
          head-variant="light"
          :fields="fields_pub"
          :items="items_pub"
          class="w-100 px-3"
        >
          <template #cell(index)="row">
            <span>{{ row.index + 1 }}</span>
          </template>
          <template #cell(cost_per_campaign)="row">
            {{ `$${row.value}` }}
          </template>
          <template #cell(website_url)="row">
            <span class="d-block" v-for="(val, index) in row.value">{{ val }}</span>
          </template>
          <template #cell(period)="row">
            <span class="d-block" v-for="(val, index) in row.value">{{ val }}{{ index == 0 ? ' ~' : '' }}</span>
          </template>
          <template #cell(sum)="row">
            {{ `$${row.value}` }}
          </template>
        </b-table>
      </b-row>
      <b-row class="mt-5" v-else>
        <b-table
          hover
          bordered
          sticky-header
          head-variant="light"
          :fields="fields_adv"
          :items="items_adv"
          class="w-100 px-3"
        >
          <template #cell(index)="row">
            <span>{{ row.index + 1 }}</span>
          </template>
          <template #cell(cost_per_campaign)="row">
            {{ `$${row.value}` }}
          </template>
          <template #cell(website_url)="row">
            <span class="d-block" v-for="(val, index) in row.value">{{ val }}</span>
          </template>
          <template #cell(period)="row">
            <span class="d-block" v-for="(val, index) in row.value">{{ val }}{{ index == 0 ? ' ~' : '' }}</span>
          </template>
          <template #cell(sum)="row">
            {{ `$${row.value}` }}
          </template>
        </b-table>
      </b-row>
      <tutorial-video />
    </b-container>
    <b-modal
      id="top-up-modal"
      hide-footer
      title="Top up Your Account"
      centered
      ref="topUpModal"
      size="md">
      <h6>To top up your account, please send an email to: </h6>
      <h6><a href="mailto:Accounting@vid-GEN.com">Accounting@vid-GEN.com</a></h6>
      <h6>An invoice will be created for you to wire/ach funds to our account. </h6>
      <h6>Your campaign may become inactive until funds have been confirmed. </h6>
      <div class="d-flex justify-content-end copy-modal">
        <b-button class="mt-3 ml-2 px-2" @click="hideTopUpModal">Close</b-button>
      </div>
    </b-modal>
  </div>
</template>

<style lang="scss" moduled>
$base-color: #1998e4;
$light-color: #f0f1f3;
#page-stat {
  padding: 30px;
  height: 100%;
  background-color: white;
  border-radius: 30px;
  .b-table-sticky-header {
    max-height: 500px;
  }
  .btn-account-top-up {
    font-size: 16px;
    font-weight: 600;
  }
  .reportrange-text {
    background: $light-color;
    border: 1px solid $light-color;
    color: black;
    font-weight: 700;
    padding: 5px 30px;
    border-radius: 30px;
  }
  .custom-select {
    width: 300px;
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
  .create-vidpop {
    display: flex;
    align-items: center;
    text-decoration: none;
    span {
      font-weight: 700;
      margin-right: 7px;
    }
    .img-wrapper {
      padding: 7px;
      width: 37px;
      height: 37px;
      background-color: $base-color;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      img {
        width: 15px;
      }
    }
  }
  .stat-card {
    border-radius: 20px;
    // display: flex;
    // align-items: center;
    justify-content: center;
    height: 210px;
    background: linear-gradient(260deg, #1b90d6 16.09%, #65a2c5 97.42%);
    box-shadow: 3px 3px 7px 4px #6361611a;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    h3 {
      font-family: 'Montserrat', serif;
      font-weight: 900;
      font-size: 3.5rem;
      color: white;
    }
    label {
      font-family: 'Montserrat', serif;
      font-weight: 500;
      font-size: 1.4rem;
      color: white;
    }
    span {
      font-family: 'Montserrat', serif;
      font-weight: 500;
      font-size: 3.5rem;
      color: white;
    }
    .stat-inner {
      text-align: center;
      padding-top: 44px;
    }
    &.active {
      background: linear-gradient(
        38.72deg,
        rgb(241 216 216 / 0%) 12.57%,
        rgb(98 171 214 / 25%) 71.7%
      );
      h3,
      label,
      span {
        color: #253849;
      }
    }
    &:hover {
      transform: scale(1.03);
    }
    &.stat-rate h3 {
      font-size: 3.5rem;
    }
  }
  .chart-wrapper {
    border-radius: 20px;
    background-color: white;
    box-shadow: 0px 0px 16px -3px rgba(24, 148, 221, 0.25);
    display: flex;
    align-content: center;
    justify-content: center;
    padding: 30px;
    width: 100%;
  }
  .col-flex-5 {
    flex: 0 0 20%;
    max-width: 20%;
  }
}
</style>

<script>
import { mapGetters } from 'vuex';
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import TutorialVideo from '@/views/components/tutorial-video';
import axios from 'axios';
import VueApexCharts from 'vue-apexcharts';

export default {
  name: 'statistics',
  components: {
    DateRangePicker,
    VueApexCharts,
    TutorialVideo
  },
  computed: {
    ...mapGetters(['currentUser', 'tutorial', 's3Storage'])
  },
  data() {
    return {
      dateRange: {
        startDate: '2023-10-01',
        endDate: '2023-10-30'
      },
      minDate: '2021-11-01',
      maxDate: '2024-11-01',
      localData: {
        firstDay: 1,
        format: 'mm/dd/yyyy',
        direction: 'ltr',
        separator: ' - '
      },
      // activeCard: 'impression',
      activeCard: '',
      vidpops: [],
      selectedVid: -1,
      impressions: 0,
      impressionData: {},
      replies: 0,
      replyData: {},
      // : 0,
      stepData: {},
      rate: 0,
      options: {
        chart: {
          id: 'vuechart-stat',
          width: '100%'
        },
        stroke: {
          curve: 'smooth'
        }
      },
      series: [
        {
          data: []
        }
      ],
      storageLeft: 0,
      showStorage: false,
      apiStartDate: '',
      apiEndDate: '',
      fields_adv: [
        { key: 'index', label: '#', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe" } },
        { key: 'publisher_name', label: 'Publisher name', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'campaign_name', label: 'Campaign name', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'cost_per_campaign', label: 'Cost per campaign', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'website_url', label: 'Website', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'impressions', label: 'Impressions', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'plays', label: 'Plays', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'leads', label: 'Leads', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'sum', label: 'Sum', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'period', label: 'Period', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
      ],
      fields_pub: [
        { key: 'index', label: '#', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe" } },
        { key: 'advertiser_name', label: 'Advertiser name', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'campaign_name', label: 'Offer', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'cost_per_campaign', label: 'Cost per offer', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'website_url', label: 'Website', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'impressions', label: 'Impressions', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'plays', label: 'Plays', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'leads', label: 'Leads', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'sum', label: 'Sum', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'period', label: 'Period', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
      ],
      items_adv: [],
      items_pub: [],
      pubTotalCampaigns: 0,
      pubVideoPlays: 0,
      pubLeads: 0,
      pubAvgViewRate: 0,
      pubRevenue: 0,

      advTotalCampaigns: 0,
      advVideoPlays: 0,
      advLeads: 0,
      advAvgViewRate: 0,
      advApproved: 0,
      advSpend: 0,
      advCost: 0,
      advBalance: 0,
    };
  },
  watch: {
    selectedVid: async function(newVal, oldVal) {
      if (newVal !== -1) {
        const activeVidpop = this.vidpops.find(item => item.vidpop_id === newVal);
        if (activeVidpop) {
          this.items_adv = [];
          this.items_pub = [];  
          this.loadVidpop()
        }
      } else { // All Vidpops
        this.vidpops = [];
        this.items_adv = [];
        this.items_pub = [];
        this.loadVidpop();
      }
      // await this.loadStatistic();
      // if (newVal !== -1) {
      //   const activeVidpop = this.vidpops.find(item => item.id === newVal);
      //   if (activeVidpop) {
      //     const res = await axios.get(`/api/step-counts/${newVal}`);
      //     if (res && res.status === 200) {
      //     }
      //   }
      // }
    }
  },
  async mounted() {
    // determine to show tutorial or not
    this.dateRange.startDate = this.$func.getDate(null, false);
    this.dateRange.endDate = this.$func.getDate(null, false)
    const date = new Date();
    var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

    if (['TIER1', 'TIER2', 'TIER3'].includes(this.currentUser.level)) {
      this.showStorage = true;
    }

    // storage left
    if (this.s3Storage < 1025) {
      this.storageLeft = `${this.s3Storage} Mbyte Left`;
    } else {
      const s3 = Math.ceil(this.s3Storage / 1024);
      this.storageLeft = `${s3}Gbyte Left`;
    }

    this.dateRange = {
      startDate: firstDay.toISOString().slice(0, 10),
      endDate: lastDay.toISOString().slice(0, 10)
    };
    this.loadVidpop();
    await this.loadStatistic();
    if (this.tutorial !== 0 && this.tutorial !== '0') {
      this.$bvModal.show('tutorial-video-modal');
    }
  },
  methods: {
    onAccountTopUp() {
      this.$bvModal.show('top-up-modal');
    },
    hideTopUpModal() {
      this.$bvModal.hide('top-up-modal');
    },
    async loadVidpop() {
      // try {
      //   const res = await axios.get('/api/vidpops');
      //   if (res && res.status === 200) {
      //     this.vidpops.push({
      //       id: -1,
      //       name: 'All Vidpops'
      //     });
      //     res.data.forEach(r => {
      //       this.vidpops.push(r);
      //     });
      //   } else {
      //     this.$func.makeToast(this, 'Error', res.data.error, 'danger');
      //   }
      // } catch (err) {
      //   this.$func.makeToast(
      //     this,
      //     'Error',
      //     'Cannot load your VidGen',
      //     'danger'
      //   );
      // }
      try {
        this.apiStartDate = this.getFormattedDateString(this.dateRange.startDate);
        this.apiEndDate = this.getFormattedDateString(this.dateRange.endDate);
        const res = await axios.get('/api/metrics?start='+this.apiStartDate+'&end='+this.apiEndDate+'&vid='+this.selectedVid);
        if (res && res.status === 200) {
          if (this.currentUser.role == 'publisher') {
            if (this.selectedVid == -1) {
              this.vidpops.push({
                vidpop_id: -1,
                name: 'All VidGen'
              });
              res.data.all_vidpops.forEach(r => {
                this.vidpops.push({vidpop_id: r.vidpopup_id, name: r.name});
              });
            }
            this.pubTotalCampaigns = res.data.total_campaigns.length
            this.pubVideoPlays = res.data.video_plays.length
            this.pubLeads = res.data.leads.length
            // this.pubAvgViewRate = res.data.all_vidpops.length > 0 ? ((res.data.views.length / res.data.all_vidpops.length) * 100).toFixed(1) : 0
            this.pubAvgViewRate = this.pubVideoPlays > 0 ? ((this.pubLeads / this.pubVideoPlays) * 100).toFixed(1) : 0
            this.pubRevenue = res.data.revenue.reduce((val, current) => val + current.cost, 0);
            res.data.table_calc.map(val => {
              this.items_pub = [...this.items_pub, {
                "advertiser_name": val.advertiser.name,
                "campaign_name": val.vidgen.name,
                "cost_per_campaign": val.vidgen.cost,
                "website_url": val.website_url.split(';'),
                "impressions": val.impression_count,
                "plays": val.metrics_view_count,
                "leads": val.metrics_click_count,
                "sum": val.vidgen.cost * val.metrics_click_count,
                "period": [val.metrics_first_click_date, val.metrics_last_click_date]
              }];
            });
          } else if (this.currentUser.role == 'agent') {
            if (this.selectedVid == -1) {
              this.vidpops.push({
                vidpop_id: -1,
                name: 'All VidGen'
              });
              res.data.all_vidpops.forEach(r => {
                this.vidpops.push({vidpop_id: r.id, name: r.name});
              });
            }
            this.advTotalCampaigns = res.data.total_campaigns.length
            this.advVideoPlays = res.data.plays_vidpopup_count.reduce((val, current) => val + current.plays, 0)
            this.advLeads = res.data.plays_leads.length
            // this.advAvgViewRate = res.data.all_vidpops.length > 0 ? ((res.data.plays_leads.length / res.data.all_vidpops.length) * 100).toFixed(1) : 0
            this.advAvgViewRate = this.advVideoPlays > 0 ? ((this.advLeads / this.advVideoPlays) * 100).toFixed(1) : 0
            this.advApproved = res.data.approved_publishers.length - res.data.banned_publishers;
            this.advSpend = 0;
            // this.advCost = this.selectedVid == -1 ? res.data.all_vidpops.reduce((val, current) => val + current.cost, 0) : res.data.all_vidpops.filter(val => val.id == this.selectedVid)[0].cost
            this.advCost = this.selectedVid == -1 ? 0 : res.data.all_vidpops.filter(val => val.id == this.selectedVid)[0].cost
            this.advBalance = res.data.balance;
            
            res.data.table_calc.map(val => {
              this.items_adv = [...this.items_adv, {
                "publisher_name": val.publisher.name,
                "campaign_name": val.vidgen.name,
                "cost_per_campaign": val.vidgen.cost,
                "website_url": val.website_url.split(';'),
                "impressions": val.impression_count,
                "plays": val.metrics_view_count,
                "leads": val.metrics_click_count,
                "sum": val.vidgen.cost * val.metrics_click_count,
                "period": [val.metrics_first_click_date, val.metrics_last_click_date]
              }];
            });
          } else {
            if (this.selectedVid == -1) {
              this.vidpops.push({
                vidpop_id: -1,
                name: 'All VidGen'
              });
              res.data.all_vidpops.forEach(r => {
                this.vidpops.push({vidpop_id: r.id, name: r.name});
              });
            }
            this.advTotalCampaigns = res.data.total_campaigns.length
            this.advVideoPlays = res.data.plays_vidpopup_count.reduce((val, current) => val + current.plays, 0)
            this.advLeads = res.data.plays_leads.length
            // this.advAvgViewRate = res.data.all_vidpops.length > 0 ? ((res.data.plays_leads.length / res.data.all_vidpops.length) * 100).toFixed(1) : 0
            this.advAvgViewRate = this.advVideoPlays > 0 ? ((this.advLeads / this.advVideoPlays) * 100).toFixed(1) : 0
            this.advApproved = res.data.approved_publishers.length - res.data.banned_publishers;
            this.advSpend = res.data.spend.reduce((val, current) => val + current.cost, 0)
            // this.advCost = this.selectedVid == -1 ? res.data.all_vidpops.reduce((val, current) => val + current.cost, 0) : res.data.all_vidpops.filter(val => val.id == this.selectedVid)[0].cost
            this.advCost = this.selectedVid == -1 ? 0 : res.data.all_vidpops.filter(val => val.id == this.selectedVid)[0].cost
            this.advBalance = res.data.balance;
            
            res.data.table_calc.map(val => {
              this.items_adv = [...this.items_adv, {
                "publisher_name": val.publisher.name,
                "campaign_name": val.vidgen.name,
                "cost_per_campaign": val.vidgen.cost,
                "website_url": val.website_url.split(';'),
                "impressions": val.impression_count,
                "plays": val.metrics_view_count,
                "leads": val.metrics_click_count,
                "sum": val.vidgen.cost * val.metrics_click_count,
                "period": [val.metrics_first_click_date, val.metrics_last_click_date]
              }];
            });
            // let table_plays_leads = res.data.table_plays_leads
            // let table_calc = res.data.table_calc
            // this.items = []
            // table_calc.map(val => {
            //   let tmp = table_plays_leads.filter(v => v.publisher_id == val.publisher_id && v.vid == val.vidpopup_id)
            //   if (tmp.length) {
            //     this.items = [...this.items, {
            //       "pub_name": val.user_name,
            //       "camp_name": val.vid_name,
            //       "plays": tmp[0].plays,
            //       "leads": tmp[0].leads,
            //       "cost": val.cost * val.cnts
            //     }]
            //   } else {
            //     this.items = [...this.items, {
            //       "pub_name": val.user_name,
            //       "camp_name": val.vid_name,
            //       "plays": 0,
            //       "leads": 0,
            //       "cost": val.cost * val.cnts
            //     }]
            //   }
            // })
          }
          let chart_data = []
            res.data.chart.map(val => {
              chart_data = [...chart_data, {
                x: this.$func.getDate(val.created_at),
                y: val.published_count
              }]
            })
            this.series = [{data: chart_data}]
        } else {
          this.$func.makeToast(this, 'Error', res.data.error, 'danger');
        }
      } catch (err) {
        console.log('err => ', err)
        this.$func.makeToast(
          this,
          'Error',
          'Cannot load your VidGen',
          'danger'
        );
      }
    },
    async loadStatistic() {
      return;
      const loader = this.$loading.show();
      try {
        const startDate = new Date(this.dateRange.startDate)
          .toISOString()
          .slice(0, 10);
        const endDate = new Date(this.dateRange.endDate)
          .toISOString()
          .slice(0, 10);
        const [impressions, replies] = await Promise.all([
          axios.post(`/api/stat-impressions`, {
            start: startDate,
            end: endDate,
            vid: this.selectedVid
          }),
          axios.post(`/api/stat-replies`, {
            start: startDate,
            end: endDate,
            vid: this.selectedVid
          })
        ]);
        if (impressions && impressions.status === 200) {
          this.impressionData = impressions.data.data;
          this.impressions = 0;
          this.impressionData.data.forEach(itm => {
            this.impressions += itm.y;
          });
        } else {
          this.$func.makeToast(this, 'Error', impressions.data.error, 'danger');
          this.impressions = 0;
          this.impressionData = this.emptyData();
        }
        if (replies && replies.status === 200) {
          this.replyData = replies.data.data;
          this.replies = 0;
          this.replyData.data.forEach(itm => {
            this.replies += itm.y;
          });
        } else {
          this.$func.makeToast(this, 'Error', replies.data.error, 'danger');
          this.replies = 0;
          this.replyData = this.emptyData();
        }
        if (this.activeCard === 'impression') {
          // impression
          this.series = [
            {
              data: this.impressionData.data
            }
          ];
        } else if (this.activeCard === 'reply') {
          this.series = [
            {
              data: this.replyData.data
            }
          ];
        } else {
          const empty = this.emptyData();
          this.series = empty.data;
        }
        this.rate =
          this.impressions > 0
            ? ((this.replies / this.impressions) * 100).toFixed(1)
            : 0;
      } catch (err) {
        // console.log(err);
      }
      loader && loader.hide();
    },
    formatDate(date) {
      const dt = new Date(date).toLocaleDateString();
      return dt;
    },
    onSelectCard(idx) {
      return;
      if (idx !== this.activeCard) {
        this.activeCard = idx;
      }
      if (this.activeCard === 'impression') {
        // impression
        this.series = [
          {
            data: this.impressionData.data
          }
        ];
      } else if (this.activeCard === 'reply') {
        this.series = [
          {
            data: this.replyData.data
          }
        ];
      } else {
        const empty = this.emptyData();
        this.series = empty.data;
      }
    },
    emptyData() {
      return {
        data: []
      };
    },
    getFormattedDateString(date) {
      let tempDate = new Date(date);
      let tempMonth = tempDate.getMonth() + 1;
      let month = tempMonth >= 10 ? tempMonth : '0' + tempMonth;
      let tempDay = tempDate.getDate();
      let day = tempDay >= 10 ? tempDay : '0' + tempDay;
      return `${tempDate.getFullYear()}-${month}-${day}`;
    },
    updateRange(e) {
      // this.loadStatistic();
      this.vidpops = [];
      this.loadVidpop();
    }
  }
};
</script>
