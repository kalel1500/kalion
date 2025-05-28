import HomeUseCase from '../application/HomeUseCase';

export default class HomeController {
    home() {
        HomeUseCase.new().__invoke();
    }
}
