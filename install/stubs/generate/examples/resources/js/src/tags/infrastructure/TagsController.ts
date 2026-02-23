import { ViewData } from "@kalel1500/kalion-js";
import { TagType } from '@/src/shared/_types';
import TagsListUseCase from '@/src/tags/application/TagsListUseCase';

export type DataViewTags = {
    currentTagType: TagType|null;
    pluckedTypes: Record<string, string>;
}

export default class TagsController {
    tags(viewData: ViewData<DataViewTags>) {
        TagsListUseCase.new(viewData.data).__invoke();
    }
}
