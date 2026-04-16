import { useState } from "react";

interface QuestionAnswer {
    question_id: number;
    value: string[];
}

export const Form = () => {
    const [isSubmitted, setIsSubmitted] = useState(false);

    const questions = [
        {
            id: 1,
            type: 'radio',
            name: 'How old are you?',
            options: [
                {
                    id: 1,
                    question_id: 1,
                    value: 'Less than 18',
                },
                {
                    id: 2,
                    question_id: 1,
                    value: '18-99',
                },
                {
                    id: 3,
                    question_id: 1,
                    value: 'More than 99',
                },
            ]
        },
        {
            id: 2,
            type: 'checkbox',
            name: 'What countries have you visited?',
            options: [
                {
                    id: 1,
                    question_id: 2,
                    value: 'Spain',
                },
                {
                    id: 2,
                    question_id: 2,
                    value: 'France',
                }
            ]
        }
    ]

    const [answers, setAnswers] = useState<QuestionAnswer[]>([])

    const handleRadioChange = (e: React.ChangeEvent<HTMLInputElement>, questionId: number) => {
        let isAlreadyAnswered = answers.find((answer) => answer.question_id === questionId) !== undefined

        if (isAlreadyAnswered) {
            setAnswers(answers.map((answer) => {
                return answer.question_id === questionId
                    ? { ...answer, value: [e.target.value] }
                    : answer;
            }))
        } else {
            setAnswers([...answers, { question_id: questionId, value: [e.target.value] }])
        }
    }

    const handleCheckboxChange = (e: React.ChangeEvent<HTMLInputElement>, questionId: number) => {
        const currentAnswer = answers.find((answer) => answer.question_id === questionId)

        if (e.target.checked) {
            if (!currentAnswer) {
                // add new answer
                setAnswers([...answers, { question_id: questionId, value: [e.target.value] }])
            } else {
                // update existing answer
                setAnswers(answers.map((answer): QuestionAnswer => {
                    return answer.question_id === questionId
                        ? { ...answer, value: [...answer.value, e.target.value] }
                        : answer
                }))
            }
        } else {
            if (currentAnswer) {
                // remove answer if unchecked the only selected option
                let updatedAnswers = answers.filter(answer => {
                    const uncheckedAllOptions = answer.question_id === questionId
                        && answer.value.length === 1
                        && answer.value[0] === e.target.value

                    return answer.question_id !== questionId || !uncheckedAllOptions;
                })

                // remove unchecked option from answer
                updatedAnswers = updatedAnswers.map(answer => 
                    answer.question_id !== questionId
                        ? answer
                        : {
                            ...answer,
                            value: answer.value.filter(selectedOption => selectedOption !== e.target.value)
                        }
                )

                setAnswers(updatedAnswers);
            }

        }
    }

    const handleSubmit = async (e: React.SubmitEvent<HTMLFormElement>) => {
        e.preventDefault();
        console.log('you submitted the form');

        const url = '/api/responses'
        const response = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(answers),
            headers: {
                "Content-Type": "application/json",
            }
        })

        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        setIsSubmitted(true);
    }

    const form =
        <form onSubmit={handleSubmit}>
            {questions.map((question) => {
                return (
                    <fieldset key={question.id}>
                        <legend>{question.name}</legend>

                        {question.options.map((option) => {
                            if (question.type === 'radio') {
                                return (
                                    <p key={option.id}>
                                        <label>
                                            {option.value}

                                            <input
                                                type={question.type}
                                                name={String(question.id)}
                                                value={option.value}
                                                onChange={(e) => handleRadioChange(e, question.id)}
                                            />
                                        </label>
                                    </p>
                                )
                            }

                            if (question.type === 'checkbox') {
                                return (
                                    <p key={option.id}>
                                        <label>
                                            {option.value}

                                            <input
                                                type={question.type}
                                                name={String(question.id)}
                                                value={option.value}
                                                onChange={(e) => handleCheckboxChange(e, question.id)}
                                            />
                                        </label>
                                    </p>
                                )
                            }

                            return null
                        })}
                    </fieldset>
                )
            })}

            <button>Submit</button>
        </form>

    return isSubmitted ? <p>Form submitted</p> : form;
}