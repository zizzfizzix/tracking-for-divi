import create_config from "@kucrut/vite-for-wp";

export default create_config({ client: "js/client/main.ts", admin: "js/admin/main.ts" }, "js/dist");
