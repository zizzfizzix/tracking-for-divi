import { v4wp } from "@kucrut/vite-for-wp";

export default {
	plugins: [
		v4wp({
			input: {
				client: "js/client/main.ts",
				admin: "js/admin/main.ts",
			},
			outDir: "js/dist",
		}),
	],
};
