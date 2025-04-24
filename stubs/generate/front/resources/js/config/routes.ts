import { Route } from '@kalel1500/kalion-js';
import HomeController from '../src/home/infrastructure/HomeController';
import PostController from "../src/posts/infrastructure/PostController";
import TagsController from "../src/tags/infrastructure/TagsController";

export function defineRoutes(): void {
    Route.page('home', [HomeController, 'home']);
    Route.page('post.list', [PostController, 'posts']);
    Route.page('tags', [TagsController, 'tags'], true);
}
