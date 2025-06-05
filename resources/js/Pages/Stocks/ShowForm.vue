<template>
    <dl class="gap-4">
        <jig-dd>
            <template #dt>Name:</template>
            {{ model.name }}
        </jig-dd>
        <jig-dd>
            <template #dt>Quantity:</template>
            {{ model.quantity }}
        </jig-dd>
            <jig-dd>
            <template #dt>Shelf:</template>
            {{ model.shelf ? model.shelf.name : '-' }}
        </jig-dd>
    </dl>
</template>

<script>
    import JigDd from "@/JigComponents/JigDd.vue";
    import InertiaButton from "@/JigComponents/InertiaButton.vue";

    import { defineComponent } from "vue";

    export default defineComponent({
        name: "ShowStocksForm",
        props: {
            model: Object,
        },
        components: {
            InertiaButton,
            JigDd,
        },
        data() {
            return {}
        },
        mounted() {},
        computed: {
            flash() {
                return this.$page.props.flash || {}
            }
        },
        methods: {}
    });
</script>

<style scoped>

</style>
