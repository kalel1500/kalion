import HomeUseCase from '@/src/home/application/HomeUseCase';

export default class HomeController {
    home() {
        HomeUseCase.new().__invoke();
    }
}
