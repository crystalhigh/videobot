<template>
	<div>
    <b-form-input
			id="collect-street-address"
			type="text"
			required
			v-model="suggestion"
			@input="getSuggestion"
			@keydown.enter="enter"
			@keydown.down="down"
			@keydown.up="up"
			@keydown.escape="escape"
			@mousedown="mousedown"
			@blur="hide"
			@focus="show"
			class="street-input"
		></b-form-input>
		<ul class="dropdown-menu w-100 dropdown-show" v-if="suggestions && suggestions.length && open">
			<li v-for="(suggestion, index) in suggestions" :key="suggestion.address_id" @mousedown.prevent="select(suggestion)" :class="{'active': isActive(index)}">
				<a class="dropdown-item">{{ suggestion.address_text }}</a>
			</li>
		</ul>
</div>
</template>
  
<style lang="scss" scoped>
	.dropdown-show {
    display: block;
		overflow-y: auto;
  }
  .dropdown-menu .active {
    color: #16181b;
    text-decoration: none;
    background-color: #e9ecef;
  }
	.street-input {
		border-radius: 20px;
	}
</style>
  
<script>
export default {
	name: 'autocomplete',
	props: {
		suggestions: [],
		fetchSuggestions: { type: Function, require: true },
	},
	data() {
		return {
			current: 0,
			suggestion: '',
			open: false,
		};
	},
	mounted() {
	},
	methods: {
		getSuggestion() {
			this.current = 0;
			this.fetchSuggestions(this.suggestion);
		},
		select(suggestion) {
			this.suggestion = suggestion.address_text;
			this.fetchSuggestions(this.suggestion);
      this.open = false;
		},
		isActive(index) {
      return this.current == index;
    },
		hide() {
			this.open = false;
		},
		show() {
			this.open = true;
		},
		mousedown() {
			if (!this.open)
				this.open = true;
		},
		escape() {
			this.open = false;
		},
		enter() {
			if (this.suggestions.length) {
				this.suggestion = this.suggestions[this.current].address_text;
				this.fetchSuggestions(this.suggestion);
			}
			this.open = false;
		},
		up() {
			this.current = (this.current - 1 + this.suggestions.length) % this.suggestions.length;
		},
		down() {
			this.current = (this.current + 1) % this.suggestions.length;
		}
	}
};
</script>