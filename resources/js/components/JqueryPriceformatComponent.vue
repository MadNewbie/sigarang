<template>
    <input type="text"
        inputmode="numeric"
        v-bind:value="this.value"
        v-on:input="$emit('input', $event.target.value)"
        v-on:change="$emit('change', $event.target.value)"
        v-on:keypress="$emit('keypress', $event)"
    >
</template>
<script>
export default {
    props: {
        opt: { required: false, type: Object},
        value: { required: false },
    },
    data() {
        return {
            defOpt: {},
        };
    },
    mounted() {
        this.defOpt = {
            prefix: '',
            suffix: '',
            centsLimit: 0,
            centsSeparator: ',',
            thousandsSeparator: '.',
        };

        let opt = this.opt;
        for (let i in opt) {
            this.defOpt[i] = opt[i];
        }

        const self = this;
        $(this.$el).priceFormat(this.defOpt);

        this.$el.addEventListener('keyup', () => {
            self.$emit('input', this.value);
        });
    },
    watch: {
        value(newVal) {
            const el = this.$el;
            const opt = this.defOpt;
            setTimeout(()=>{
                $(el).priceFormat(opt);
            },0);
        }
    },
}
</script>
