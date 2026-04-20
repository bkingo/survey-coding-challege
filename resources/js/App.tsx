import { Form } from "./Form"
import { Results } from "./Results";

export const App = () => {
    const locationPath = window.location.pathname;

    let content: React.ReactElement;

    switch (locationPath) {
        case '/form':
            content = <Form />;
            break;
        case '/results':
            content = <Results />;
            break;
        default:
            content = <p>Page not found</p>;
            break;
    }
    
    return (
        <>
            <h1 className="text-2xl font-bold">Survey Coding Challenge</h1>
            <main>{content}</main>
        </>
    )
}