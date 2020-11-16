<template>
	<b-modal
		v-model="showModal"
		title="Hello"
		:ok-title="__('2fa.verify').capitalize()"
		:cancel-title="__('base.cancel').capitalize()"
		@ok="verify"
	>
		<template slot="modal-title">
			{{ __('2fa.verify').capitalize() }}
		</template>
		<div class="d-flex justify-content-around">
			<div
				class="rounded-circle my-3 text-center"
				style="background: #d3dde6;height:100px;width:100px;line-height: 100px;"
			>
				<lit-fa-icon
					icon="lock"
					class="text-secondary"
					style="font-size: 50px;display: inline-block;vertical-align: middle;transform: translateY(-3px);"
				/>
			</div>
		</div>
		<div>
			<b-form-group :label-for="`otp-${field.id}`">
				<template slot="label">
					{{
						user.two_fa_enabled
							? __('2fa.code')
							: __('base.password')
					}}
				</template>
				<b-form-input
					:id="`otp-${field.id}`"
					@input="update"
					:type="user.two_fa_enabled ? 'text' : 'password'"
					:placeholder="user.two_fa_enabled ? '000 000' : '***'"
					:state="state"
				></b-form-input>
				<b-form-invalid-feedback
					v-for="(message, key) in messages"
					:key="key"
					:style="`display:${state == null ? 'none' : 'block'}`"
				>
					{{ message }}
				</b-form-invalid-feedback>
			</b-form-group>
		</div>
	</b-modal>
</template>

<script>
export default {
	name: 'LitFieldVerify',
	props: {
		field: {
			required: true,
			type: Object,
		},
		value: {
			required: true,
		},
	},
	data() {
		return {
			/**
			 * Determines if the modal is shown.
			 */
			showModal: false,

			/**
			 * Error stat.
			 */
			state: null,

			/**
			 * Error messages.
			 */
			messages: [],
		};
	},
	computed: {
		user() {
			return Lit.user();
		},
	},
	beforeMount() {
		Lit.bus.$on('saved', this.checkForErrors);
	},
	methods: {
		verify(e) {
			e.preventDefault();

			Lit.bus.$emit('save');
		},

		update(value) {
			this.$emit('input', value);
		},

		resetErrors() {
			this.showModal = false;
			this.state = null;
			this.messages = [];
		},

		/**
		 * Check results for erros.
		 *
		 * @param {Array} results
		 * @return
		 */
		checkForErrors(results) {
			let result = results.findFailed(
				this.field._method,
				this.field.route_prefix
			);

			if (!result) {
				return this.resetErrors();
			}

			if (!result.isAxiosError) {
				return this.resetErrors();
			}

			let errors = this.findErrors(result);
			if (!errors || _.isEmpty(errors)) {
				return this.resetErrors();
			}

			if (this.showModal === true) {
				this.state = false;
				this.messages = errors;
			}

			this.showModal = true;
		},

		/**
		 * Find errors.
		 *
		 * @param {Object} result
		 * @return {Array}
		 */
		findErrors(result) {
			if (typeof result.response.data != typeof {}) {
				return;
			}
			if (!('errors' in result.response.data)) {
				return;
			}
			if (!this.field.translatable) {
				return result.response.data.errors[this.field.local_key];
			}
			let errors = [];
			for (let key in result.response.data.errors) {
				let error = result.response.data.errors[key];
				if (key.endsWith('.' + this.field.local_key)) {
					for (let i in error) {
						let message = error[i];
						errors.push(message);
					}
				}
			}
			return errors;
		},
	},
};
</script>
