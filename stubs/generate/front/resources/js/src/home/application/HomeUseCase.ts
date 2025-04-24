import { Instantiable } from "@kalel1500/kalion-js";

export default class HomeUseCase extends Instantiable
{
    __invoke() {
        console.log('inicio');
    }
}
