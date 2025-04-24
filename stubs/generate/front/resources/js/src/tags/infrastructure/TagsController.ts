import { ViewData } from "@kalel1500/kalion-js";
import TagsListUseCase from "../application/TagsListUseCase";
import { TagType } from "../../shared/domain/entities/TagType";

export type DataViewTags = {
    currentTagType: TagType|null;
    pluckedTypes: Record<string, string>;
}

export default class TagsController {
    tags(viewData: ViewData<DataViewTags>) {
        TagsListUseCase.new(viewData.data).__invoke();
    }
}
