import create_config from "@kucrut/vite-for-wp";

export default create_config("js/src/main.ts", "js/dist", {
  build: { sourcemap: "hidden" },
});
